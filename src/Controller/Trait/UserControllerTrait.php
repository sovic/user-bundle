<?php

namespace UserBundle\Controller\Trait;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use UserBundle\Entity\User;
use UserBundle\Form\Type\ForgotPassword;
use UserBundle\Form\Type\NewPassword;
use UserBundle\Form\Type\SignIn;
use UserBundle\Form\Type\SignUp;
use UserBundle\User\UserFactory;

trait UserControllerTrait
{
    protected bool $emailVerificationEnabled = true;
    protected string $fromEmail = 'noreply@sluzba.cz';
    protected bool $requiresActivation = false;

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/user/sign-up', name: 'user_sign_up')]
    public function signup(
        EntityManagerInterface      $em,
        Environment                 $twig,
        MailerInterface             $mailer,
        Request                     $request,
        UserFactory                 $userFactory,
        UserPasswordHasherInterface $passwordHasher,
        TranslatorInterface         $t
    ): Response {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('user_dashboard');
        }

        $template = 'user/sign-up.html.twig';
        if (!$twig->getLoader()->exists($template)) {
            $template = '@UserBundle/user/sign-up.html.twig';
        }

        $form = $this->createForm(SignUp::class);
        $form->handleRequest($request);
        $this->assign('sign_up_form', $form->createView());

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render($template);
        }

        $data = $form->getData();
        $email = $data['email'];
        $validator = new EmailValidator();
        if (!$validator->isValid($email, new RFCValidation())) {
            $this->addFlash('error', $t->trans('user.sign_up.invalid_email', [], 'user-bundle'));

            return $this->render($template);
        }

        if ($userFactory->loadByEmail($email)) {
            $this->addFlash('error', $t->trans('user.sign_up.email_exists', [], 'user-bundle'));

            return $this->render($template);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setCreateDate(new DateTimeImmutable());
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $em->persist($user);
        $em->flush();

        $user = $userFactory->loadByEmail($email);
        $emailVerificationEnabled = $this->emailVerificationEnabled;
        if ($emailVerificationEnabled) {
            // send email verification email
            $salt = $request->server->get('APP_SECRET');
            $user->setEmailVerificationCode($salt);
        } else {
            $user->setEmailVerified();
        }

        $email = $user->getRegistrationEmail();
        $email->addFrom($this->fromEmail);
        $email->addReplyTo($this->fromEmail);
        $mailer->send($email);

        if ($emailVerificationEnabled) {
            $msg = 'user.sign_up.success_email_verification';
        } else {
            $user->setEmailVerified();
            if ($this->requiresActivation) {
                $msg = 'user.sign_up.success_requires_activation';
            } else {
                $user->setEnabled(true);
                $msg = 'user.sign_up.success';
            }
        }
        $this->addFlash('success', $t->trans($msg, [], 'user-bundle'));

        return $this->redirectToRoute('user_sign_up');
    }

    #[Route('/user/verify-email/{code}', name: 'user_verify_email', requirements: ['code' => '[A-Za-z0-9]{32}'])]
    public function verifyEmail(
        string              $code,
        Environment         $twig,
        TranslatorInterface $t,
        UserFactory         $userFactory
    ): Response {
        $template = 'user/verify-email.html.twig';
        if (!$twig->getLoader()->exists($template)) {
            $template = '@UserBundle/user/verify-email.html.twig';
        }

        $user = $userFactory->loadByEmailVerificationCode($code);
        if (null === $user) {
            $this->addFlash('error', $t->trans('user.verify_email.invalid_code', [], 'user-bundle'));

            return $this->render($template);
        }

        $user->setEmailVerified();

        if ($this->requiresActivation) {
            $msg = 'user.verify_email.success_requires_activation';
        } else {
            $user->setEnabled(true);
            $msg = 'user.verify_email.success';
        }
        $this->addFlash('success', $t->trans($msg, [], 'user-bundle'));

        return $this->render($template);
    }

    #[Route('/user/sign-in', name: 'user_sign_in')]
    public function signIn(
        AuthenticationUtils $authenticationUtils,
        Environment         $twig,
        TranslatorInterface $t
    ): Response {
        $template = 'user/sign-in.html.twig';
        if (!$twig->getLoader()->exists($template)) {
            $template = '@UserBundle/user/sign-in.html.twig';
        }

        if (null !== $this->getUser()) {
            return $this->redirectToRoute('user_dashboard');
        }

        // get the login error if there is one
        $formError = null;
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $formError = $t->trans($error->getMessageKey(), $error->getMessageData(), 'security');
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $signInForm = $this->createForm(SignIn::class);
        if ($lastUsername) {
            $signInForm->setData(['email' => $lastUsername]);
        }

        $this->assign('form_error', $formError);
        $this->assign('last_username', $lastUsername);
        $this->assign('sign_in_form', $signInForm->createView());

        return $this->render($template);
    }

    #[Route('/user/dashboard', name: 'user_dashboard')]
    public function dashboard(
        Environment $twig
    ): Response {
        // TODO authorization via route check
        if (null === $this->getUser()) {
            return $this->redirectToRoute('user_sign_in');
        }

        $template = 'user/dashboard.html.twig';
        if (!$twig->getLoader()->exists($template)) {
            $template = '@UserBundle/user/dashboard.html.twig';
        }

        $this->assign('user', $this->getUser());

        return $this->render($template);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/user/forgot-password', name: 'user_forgot_password')]
    public function forgotPassword(
        Environment         $twig,
        MailerInterface     $mailer,
        Request             $request,
        TranslatorInterface $t,
        UserFactory         $userFactory
    ): Response {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('user_dashboard');
        }

        $template = 'user/forgot-password.html.twig';
        if (!$twig->getLoader()->exists($template)) {
            $template = '@UserBundle/user/forgot-password.html.twig';
        }

        $form = $this->createForm(ForgotPassword::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userFactory->loadByEmail($data['email']);
            if ($user) {
                $salt = $request->server->get('APP_SECRET');
                $user->setForgotPasswordCode($salt);

                $email = $user->getForgotPasswordEmail();
                $email->addFrom($this->fromEmail);
                $email->addReplyTo($this->fromEmail);
                $mailer->send($email);

                $this->addFlash('success', $t->trans('user.forgot_password.success', [], 'user-bundle'));

                return $this->redirectToRoute('user_forgot_password');
            }
            $this->assign('form_error', $t->trans('user.forgot_password.invalid_email', [], 'user-bundle'));
        }
        $this->assign('forgot_password_form', $form->createView());

        return $this->render($template);
    }

    #[Route('/user/new-password/{code}', name: 'user_new_password', requirements: ['code' => '[A-Za-z0-9]{32}'])]
    public function newPassword(
        string                      $code,
        Environment                 $twig,
        Request                     $request,
        TranslatorInterface         $t,
        UserFactory                 $userFactory,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('user_dashboard');
        }

        $template = 'user/new-password.html.twig';
        if (!$twig->getLoader()->exists($template)) {
            $template = '@UserBundle/user/new-password.html.twig';
        }

        $user = $userFactory->loadByForgotPasswordCode($code);
        if (null === $user) {
            $this->assign('form_error', $t->trans('user.forgot_password.invalid_code', [], 'user-bundle'));

            return $this->render($template);
        }

        $form = $this->createForm(NewPassword::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->entity->setPassword($passwordHasher->hashPassword($user->entity, $data['password']));
            $user->entity->setForgotPasswordCode(null);
            $user->setEmailVerified();

            $this->addFlash('success', $t->trans('user.new_password.success', [], 'user-bundle'));

            return $this->redirectToRoute('user_sign_in');
        }

        $this->assign('new_password_form', $form->createView());

        return $this->render($template);
    }

    #[Route('/user/logout', name: 'user_logout')]
    public function logout(): void
    {
        // controller can be blank: it will never be executed!
        throw new LogicException('Don\'t forget to activate logout in security.yaml');
    }
}
