<?php

namespace AppBundle\Security;

use AppBundle\Entity\AuthToken;
use AppBundle\Entity\AuthUser;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class AuthTokenUserProvider
 */
class AuthTokenUserProvider implements UserProviderInterface
{
    /**
     * @var EntityRepository
     */
    private $authTokenRepository;

    /**
     * @var EntityRepository
     */
    private $userRepository;

    /**
     * AuthTokenUserProvider constructor.
     *
     * @param EntityRepository $authTokenRepository
     * @param EntityRepository $userRepository
     */
    public function __construct(EntityRepository $authTokenRepository, EntityRepository $userRepository)
    {
        $this->authTokenRepository = $authTokenRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $authTokenHeader
     *
     * @return AuthToken
     */
    public function getAuthToken($authTokenHeader)
    {
        return $this->authTokenRepository->findOneBy(
            [
                'value' => $authTokenHeader,
            ]
        );
    }

    /**
     * @param string $login
     *
     * @return AuthUser
     */
    public function loadUserByUsername($login)
    {
        return $this->userRepository->findOneBy(
            [
                'login' => $login,
            ]
        );
    }

    /**
     * @param UserInterface $user
     * @return void
     */
    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    /**
     * @param $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'AppBundle\Entity\AuthUser' === $class;
    }
}