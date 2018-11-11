<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.06.2018
 * Time: 8:40
 */

namespace AppBundle\Security;


use AppBundle\Entity\User;
use AppBundle\Entity\UserActivation;
use AppBundle\Exception\BadCodeCheckingAttemptException;
use AppBundle\Exception\CodeExpiredException;
use AppBundle\Exception\InsecureCodeCreationAttemptException;
use AppBundle\Exception\InvalidUsernameException;
use AppBundle\Exception\NoUserForCheckingException;
use AppBundle\Exception\OutOfCheckingAttemptsException;
use AppBundle\Exception\UserExistsException;
use AppBundle\Service\SMSSender;
use AppBundle\Validation\UsernameType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserActivator implements UserActivatorInterface
{

    /**
     * @var
     */
    const INITIAL_ATTEMPTS_COUNT = 3;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @var SMSSender
     */
    protected $sender;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * UserActivator constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SMSSender $sender
     * @param ContainerInterface $container
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, SMSSender $sender, ContainerInterface $container)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->sender = $sender;
        $this->container = $container;
    }

    /**
     * @param String $username
     * @return array
     */
    public function createUserActivation(String $username)
    {
        if ($this->em->getRepository(User::class)->findOneBy(['username' => $username]))  {
            throw new UserExistsException();
        }

        /**
         * @var UserActivation $userActivation
         */
        if ($userActivation = $this->em->getRepository(UserActivation::class)->findOneBy(['username' => $username])) {
            if ($userActivation->secureCodeCreationAttempt()) {
                $usernameType = $userActivation->getUsernameType();

                $activationCode = ($userActivation->getUsernameType() === UsernameType::TYPE_PHONE)
                    ? $this->generateCodeForSMS()
                    : $this->generateCodeForEmail($username);
            } else {
                throw new InsecureCodeCreationAttemptException();
            }
        } else {
            $usernameType = UsernameType::getType($username);

            if (!$usernameType) {
                throw new InvalidUsernameException();
            }

            $activationCode = ($usernameType === UsernameType::TYPE_PHONE)
                ? $this->generateCodeForSMS()
                : $this->generateCodeForEmail($username);

            $userActivation = new UserActivation();

            $userActivation->setUsername($username);
            $userActivation->setUsernameType($usernameType);
        }

        $userActivation->setCode($activationCode);
        $userActivation->setAttemptsCount(static::INITIAL_ATTEMPTS_COUNT);
        $userActivation->setLastCodeRequestedAt(time());
        $userActivation->setCodeValidTill(time() + 120);

        if ($usernameType == UsernameType::TYPE_EMAIL) {
            $userActivation->setCodeValidTill(time() + 60 * 60 * 24);
        }

        $this->em->persist($userActivation);
        $this->em->flush();

        if ($usernameType == UsernameType::TYPE_PHONE && $this->container->get('kernel')->getEnvironment() == 'prod') {
            $this->sender->send_sms($username, 'Код подтверждения: ' . $activationCode);
        }

        return [
            'message' => $this->getMessage($usernameType),
            'activationCode' => $activationCode
        ];
    }

    /**
     * @param String $enteredCode
     * @param String|null $username
     * @return User
     */
    public function checkUserActivation(String $enteredCode, String $username = null)
    {
        if (null === $username) {
            /**
             * @var UserActivation $userActivation
             */
            if ($userActivation = $this->em->getRepository(UserActivation::class)->findOneBy(['code' => $enteredCode])) {
                if ($userActivation->secureCodeValidTime()) {
                    return $this->registerUser($userActivation);
                } else {
                    throw new CodeExpiredException();
                }
            } else {
                throw new NoUserForCheckingException();
            }
        } else {
            /**
             * @var UserActivation $userActivation
             */
            if ($userActivation = $this->em->getRepository(UserActivation::class)->findOneBy(['username' => $username])) {
                if ($userActivation->secureCodeValidTime()) {
                    if ($userActivation->secureCodeAttemptsCount()) {
                        if ($enteredCode == $userActivation->getCode()) {
                            return $this->registerUser($userActivation);
                        } else {
                            $userActivation->setAttemptsCount($userActivation->getAttemptsCount() - 1);

                            $this->em->persist($userActivation);
                            $this->em->flush();

                            throw new BadCodeCheckingAttemptException([
                                'attemptsCount' => $userActivation->getAttemptsCount()
                            ]);
                        }
                    } else {
                        throw new OutOfCheckingAttemptsException();
                    }
                } else {
                    throw new CodeExpiredException();
                }
            } else {
                throw new NoUserForCheckingException();
            }
        }
    }

    protected function registerUser(UserActivation $userActivation)
    {
        $user = new User();

        $password = static::generatePassword();

        if ($this->container->get('kernel')->getEnvironment() == 'dev') {
            $password = 'test';
        }

        $user->setUsername($userActivation->getUsername());
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        if ($userActivation->getUsernameType() == UsernameType::TYPE_PHONE) {
            $user->setPhone($userActivation->getUsername());
        } else {
            $user->setEmail($userActivation->getUsername());
        }

        $user->setIsActivated(true);
        $user->setModerationStatus(2);
        $user->setPublicationStatus(2);
        $user->setCreatedAt(time());
        $user->setIsContractor(false);
        $user->setApiToken(static::generateUniqueToken($user->getUsername()));

        if ($userActivation->getUsernameType() == UsernameType::TYPE_PHONE) {
            $this->sender->send_sms(
                $user->getUsername(),
                'Вы зарегистрированы на asr.dev.ru. Ваш пароль (пожалуйста, поменяйте его при первом входе): ' . $password);
        }

        $this->em->persist($user);
        $this->em->remove($userActivation);

        $this->em->flush();

        return $user;
    }

    protected function getMessage($usernameType)
    {
        return ($usernameType == UsernameType::TYPE_PHONE)
            ? 'Код отправлен на Ваш номер и будет действителен в течение 1 минуты'
            : 'Ссылка для завершения регистрации отправлена Вам на почту и будет действительна в течение 24 часов';
    }

    protected function generateCodeForSMS()
    {
        return static::generateNumber();
    }

    protected function generateCodeForEmail($username)
    {
        return static::generateUniqueToken($username);
    }

    public static function generateUniqueToken(String $salt)
    {
        return md5(static::generateNumber()) . md5($salt) . md5(time()) . uniqid() . md5($salt . time());
    }

    public static function generateNumber()
    {
        return 123123;
        //return mt_rand(111111, 999999);
    }

    public static function generatePassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

}