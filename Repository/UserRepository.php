<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 09.07.2018
 * Time: 14:43
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Contractor;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRepository extends EntityRepository
{

    public function findByToken($token = null)
    {
        return $this->getEntityManager()
            ->getConnection()
            ->createQueryBuilder()
            ->select('u.id, u.username, u.email, u.phone, u.avatar_path AS avatarPath,
                    u.is_activated AS isActivated, u.first_name AS firstName, u.region AS regionId,
                    u.city AS cityId, u.created_at AS createdAt, u.is_contractor AS isContractor,
                    con.id AS contractorId, con.is_autoservice AS isAutoservice, con.name AS name
              ')
            ->from('users', 'u')
            ->where('u.api_token = :api_token')
            ->setParameter(':api_token', $token)
            ->leftJoin('u', 'contractor', 'con','u.id = con.user_id')
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);
    }

    public function save($data, UserPasswordEncoderInterface $passwordEncoder)
    {
        /**
         * @var User $user
         */
        $user = $data['user'];

        if (array_key_exists('firstName', $data)) {
            $user->setFirstName($data['firstName']);
        }

        if (array_key_exists('phone', $data)) {
            $user->setPhone($data['phone']);
        }

        if (array_key_exists('email', $data)) {
            $user->setEmail($data['email']);
        }

        if (array_key_exists('password', $data)) {
            $passwordNew = $passwordEncoder->encodePassword($user, $data['password']);

            $user->setPassword($passwordNew);
        }

        if ($user->getisContractor()) {
            /**
             * @var Contractor $contractor
             */
            $contractor = $this->getEntityManager()->getRepository(Contractor::class)
                ->findOneBy(['user' => $user]);

            if (array_key_exists('name', $data)) {
                $contractor->setName($data['name']);
            }

            if (array_key_exists('isAutoservice', $data)) {
                $contractor->setIsAutoservice($data['isAutoservice']);
            }

            $this->getEntityManager()->persist($contractor);
        }

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

}