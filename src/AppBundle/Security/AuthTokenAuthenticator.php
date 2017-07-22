<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\HttpUtils;
use AppBundle\Entity\AuthToken;

/**
 * @package AppBundle\Security
 */
class AuthTokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * Validation time of token 10 min
     */
    const TOKEN_VALIDITY_DURATION = 600;

    /**
     * @var HttpUtils
     */
    private $httpUtils;

    /**
     * @param HttpUtils $httpUtils
     */
    public function __construct(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }

    /**
     * @param Request $request
     * @param $providerKey
     *
     * @return PreAuthenticatedToken|null
     */
    public function createToken(Request $request, $providerKey)
    {
        $targetUrl = '/auth-tokens';
        // Si la requête est une création de token, aucune vérification n'est effectuée
        if ($request->getMethod() === "POST" && $this->httpUtils->checkRequestPath($request, $targetUrl)) {
            return null;
        }

        $authTokenHeader = $request->headers->get('X-Auth-Token');

        if (!$authTokenHeader) {
            throw new BadCredentialsException('X-Auth-Token header is required');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $authTokenHeader,
            $providerKey
        );
    }

    /**
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param $providerKey
     *
     * @return PreAuthenticatedToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof AuthTokenUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of AuthTokenUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $authTokenHeader = $token->getCredentials();
        $authToken = $userProvider->getAuthToken($authTokenHeader);

        if (!$authToken || !$this->isTokenValid($authToken)) {
            throw new BadCredentialsException('Invalid authentication token');
        }

        $user = $authToken->getAuthUser();
        $pre = new PreAuthenticatedToken(
            $user,
            $authTokenHeader,
            $providerKey,
            $user->getRoles()
        );

        // Our user doesn't have role we must to authenticate them
        $pre->setAuthenticated(true);

        return $pre;
    }

    /**
     * @param TokenInterface $token
     * @param $providerKey
     *
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @throws AuthenticationException
     * @return void
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // If authentication failed throw an exception
        throw $exception;
    }

    /**
     * Vérifie la validité du token
     * @param AuthToken $authToken
     *
     * @return bool
     */
    private function isTokenValid(AuthToken $authToken)
    {
        return (time() - $authToken->getCreatedAt()->getTimestamp()) < self::TOKEN_VALIDITY_DURATION;
    }
}