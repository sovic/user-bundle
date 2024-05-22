<?php

namespace UserBundle\Controller\Trait;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use UserBundle\Form\Type\UserSettings;
use UserBundle\Form\Type\UserSettingsNewPassword;
use UserBundle\User\UserFactory;

trait SettingsControllerTrait
{
    #[Route('/user/settings', name: 'user_settings')]
    public function index(
        Environment                 $twig,
        Request                     $request,
        TranslatorInterface         $t,
        UserFactory                 $userFactory,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if (null === $this->getUser()) {
            return $this->redirectToRoute('user_sign_in');
        }
        $user = $userFactory->loadByAuthUser($this->getUser());

        $userSettingsForm = $this->createForm(UserSettings::class);
        if (empty($userSettingsForm->getData())) {
            $userSettingsForm->setData([
                'is_emailing_enabled' => $user->entity->isEmailingEnabled(),
                'username' => $user->entity->getUsername(),
            ]);
        }
        $userSettingsFormError = null;
        $userSettingsForm->handleRequest($request);
        if ($userSettingsForm->isSubmitted() && $userSettingsForm->isValid()) {
            $data = $userSettingsForm->getData();
            $username = $data['username'];
            if (!empty($username) && $username !== $user->entity->getUsername()) {
                $user->entity->setUsername($username);
            }
            $user->entity->setIsEmailingEnabled((bool) $data['is_emailing_enabled']);
            $user->flush();
            $this->addFlash('success', $t->trans('user.settings.success', [], 'user-bundle'));

            return $this->redirectToRoute('user_settings');
        }

        $newPasswordForm = $this->createForm(UserSettingsNewPassword::class);
        $newPasswordFormError = null;
        $newPasswordForm->handleRequest($request);
        if ($newPasswordForm->isSubmitted() && $newPasswordForm->isValid()) {
            $data = $newPasswordForm->getData();
            if (!$passwordHasher->isPasswordValid($user->entity, $data['password'])) {
                $newPasswordFormError = $t->trans('user.settings.invalid_password', [], 'user-bundle');
            }
            if (empty($data['new_password'])) {
                $newPasswordFormError = $t->trans('user.settings.invalid_new_password', [], 'user-bundle');
            }
            if (null === $newPasswordFormError) {
                $newPassword = $passwordHasher->hashPassword($user->entity, $data['new_password']);
                $user->entity->setPassword($newPassword);
                $user->flush();
                $this->addFlash('success', $t->trans('user.settings.success', [], 'user-bundle'));

                return $this->redirectToRoute('user_settings');
            }
        }

        $template = 'user/settings.html.twig';
        if (!$twig->getLoader()->exists($template)) {
            $template = '@UserBundle/user/settings.html.twig';
        }

        $this->assign('user', $user->entity);
        $this->assign('user_settings_form_error', $userSettingsFormError);
        $this->assign('user_settings_form', $userSettingsForm->createView());
        $this->assign('new_password_form_error', $newPasswordFormError);
        $this->assign('new_password_form', $newPasswordForm->createView());

        return $this->render($template);
    }
}
