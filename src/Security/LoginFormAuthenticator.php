<?php

namespace UserBundle\Security;

use UserBundle\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use UserBundle\User\UserRepository;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'user_sign_in';

    private UserRepository $userRepository;

    public function __construct(
        private readonly CsrfTokenManagerInterface   $csrfTokenManager,
        private readonly EntityManagerInterface      $entityManager,
        private readonly TranslatorInterface         $translator,
        private readonly UrlGeneratorInterface       $urlGenerator,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        /** @var UserRepository $repo */
        $repo = $entityManager->getRepository(User::class);
        $this->userRepository = $repo;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    #[ArrayShape(['email' => 'string', 'password' => 'string', 'csrf_token' => 'string'])]
    public function getCredentials(Request $request): array
    {
        $query = $request->request->all('sign_in');
        $credentials = [
            'email' => $query['email'],
            'password' => $query['password'],
            'csrf_token' => $query['_csrf_token'],
        ];
        $request->getSession()->set(
            SecurityRequestAttributes::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function checkCredentials(array $credentials, PasswordAuthenticatedUserInterface $user): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword(array $credentials): ?string
    {
        return $credentials['password'];
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);
        $token = new CsrfToken('sign_in', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        return new Passport(
            new UserBadge($credentials['email'], function ($userIdentifier) {
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);
                if (!$user || !$user->isEnabled()) {
                    throw new CustomUserMessageAuthenticationException(
                        $this->translator->trans('user.sign_up.invalid_email', [], 'user-bundle')
                    );
                }

                return $user;
            }),
            new PasswordCredentials($credentials['password'])
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $this->userRepository->findOneBy(['email' => $token->getUserIdentifier()]);
        if ($user && $this->passwordHasher->needsRehash($user)) {
            $password = $this->getPassword($this->getCredentials($request));
            $this->userRepository->upgradePassword($user, $this->passwordHasher->hashPassword($user, $password));
        }

        $user->setLastLoginDate(new DateTimeImmutable());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('user_dashboard'));
    }
}
