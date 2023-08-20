<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    protected $csrfTokenManager;
    protected $entityManager;
    protected $passwordEncoder;


    public function __construct(private UrlGeneratorInterface $urlGenerator ,private ManagerRegistry $registry)
    {
//        parent::__construct($registry, User::class);
    }




    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email'],

        );

        return $credentials;
    }


    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('User could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }


    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }
    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
//        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
//            return new RedirectResponse($targetPath);
//        }
//
//        return new RedirectResponse($this->urlGenerator->generate('app_home'));
        $repo = $this->registry->getRepository(User::class);

        $user_email= $request->getSession()->get(Security::LAST_USERNAME);
        $user = $repo->findOneBy(['email'=>$user_email]);

        if($user->getCategory()->getStatus() == 1) {
            if ($user->getRoles() == ["ROLE_USER"])
                return new RedirectResponse($this->urlGenerator->generate('app_event_index_front'));

            else if ($user->getRoles() == ["ROLE_ARTISTE"]) {
                return new RedirectResponse($this->urlGenerator->generate('app_artiste_index'));

            }
        }


            return new RedirectResponse($this->urlGenerator->generate('app_home'));



    }

    protected function getLoginUrl(Request $request): string
    {

        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

//    public function findUserByEmail($email , UserRepository $userRepository){
//
//        $data = $userRepository->findOneBy(['email'=>$email]);
//
//        return $data;
//    }


}
