<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * {@see https://symfony.com/doc/current/security/custom_authenticator.html}
 */
class SessionAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        $session = $request->getSession();
        if ($request->isXmlHttpRequest() && $session->isStarted()) {
            return $session->has('user_identifier');
        }
        return false;
    }

    public function authenticate(Request $request): Passport
    {
        $uniqueId = $request->getSession()->get('user_identifier');
        return new SelfValidatingPassport(new UserBadge($uniqueId));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw new BadCredentialsException('Invalid credentials.');
    }
}
