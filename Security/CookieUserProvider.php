<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 31.01.18
 * Time: 3:24
 */

namespace AppBundle\Security;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CookieUserProvider implements UserProviderInterface
{

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUsernameForApiKey($apiKey)
    {
        /**
         * @var User $user
         */
        $user = $this->em->getRepository(User::class)->findOneBy(['apiToken' => $apiKey]);

        if ($user) {
            return $user->getUsername();
        }

        return null;
    }

    public function loadUserByUsername($username)
    {
        return $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }


}