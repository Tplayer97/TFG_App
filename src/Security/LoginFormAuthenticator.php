<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class LoginFormAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    /**
     * @var UserRepository
     */
    private $userRepo;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(UserRepository $userRepo, RouterInterface $router){

        $this->userRepo = $userRepo;
        $this->router = $router;
    }
    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === "/login" && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('username');
        $password = $request->request->get('password');
        return new Passport(
            new UserBadge($email, function($userIdentifier){
                $user = $this->userRepo->findOneBy(['username' => $userIdentifier]);

                if (!$user) throw new UserNotFoundException();

                return $user;
            }),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        ($rol = $token->getUser()->getRoles());
        //dd($rol);
        if (in_array('ROLE_GESTOR', $rol))
        return new RedirectResponse(
            $this->router->generate('app_forum_list')
        );
        else return new RedirectResponse(
            $this->router->generate('app_admin')
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        return new RedirectResponse(
            $this->router->generate('app_login')
        );
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            $this->router->generate('app_login')
        );
    }
}
