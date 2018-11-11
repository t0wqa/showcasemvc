<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 31.01.18
 * Time: 3:04
 */

namespace AppBundle\Security;


use Psr\Log\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class CookieAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    public function createToken(Request $request, $providerKey)
    {
        $apiToken = $request->headers->get('Auth-Token') ?? $request->cookies->get('asr_authentication_token');
        //$apiToken = $request->headers->get('Auth-Token');

        if (!$apiToken) {
            throw new BadCredentialsException();
        }

        return new PreAuthenticatedToken('.anon', $apiToken, $providerKey);
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof CookieUserProvider) {
            throw new InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiKeyUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $apiToken = $token->getCredentials();
        $username = $userProvider->getUsernameForApiKey($apiToken);

        if (!$username) {
            // CAUTION: this message will be returned to the client
            // (so don't put any un-trusted messages / error strings here)
            throw new CustomUserMessageAuthenticationException(
                sprintf('API Key "%s" does not exist.', $apiToken)
            );
        }

        $user = $userProvider->loadUserByUsername($username);

        return new PreAuthenticatedToken(
            $user,
            $apiToken,
            $providerKey,
            $user->getRoles()
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(
        // this contains information about *why* authentication failed
        // use it, or return your own message
            strtr($exception->getMessageKey(), $exception->getMessageData()),
            401
        );
    }


}