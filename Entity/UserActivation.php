<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 20.06.2018
 * Time: 18:00
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_activation")
 */
class UserActivation
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $username;
    /**
     * @ORM\Column(type="smallint")
     */
    protected $usernameType;

    /**
     * @ORM\Column(type="string")
     */
    protected $code;

    /**
     * @ORM\Column(type="integer")
     */
    protected $codeValidTill;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $attemptsCount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lastAttemptAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lastCodeRequestedAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsernameType()
    {
        return $this->usernameType;
    }

    /**
     * @param mixed $usernameType
     */
    public function setUsernameType($usernameType): void
    {
        $this->usernameType = $usernameType;
    }


    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCodeValidTill()
    {
        return $this->codeValidTill;
    }

    /**
     * @param mixed $codeValidTill
     */
    public function setCodeValidTill($codeValidTill): void
    {
        $this->codeValidTill = $codeValidTill;
    }

    /**
     * @return mixed
     */
    public function getAttemptsCount()
    {
        return $this->attemptsCount;
    }

    /**
     * @param mixed $attemptsCount
     */
    public function setAttemptsCount($attemptsCount): void
    {
        $this->attemptsCount = $attemptsCount;
    }

    /**
     * @return mixed
     */
    public function getLastAttemptAt()
    {
        return $this->lastAttemptAt;
    }

    /**
     * @param mixed $lastAttemptAt
     */
    public function setLastAttemptAt($lastAttemptAt): void
    {
        $this->lastAttemptAt = $lastAttemptAt;
    }

    /**
     * @return mixed
     */
    public function getLastCodeRequestedAt()
    {
        return $this->lastCodeRequestedAt;
    }

    /**
     * @param mixed $lastCodeRequestedAt
     */
    public function setLastCodeRequestedAt($lastCodeRequestedAt): void
    {
        $this->lastCodeRequestedAt = $lastCodeRequestedAt;
    }

    public function secureCodeCheckingAttempt()
    {
        return $this->attemptsCount > 0 && time() - $this->codeValidTill > 0;
    }

    public function secureCodeValidTime()
    {
        return $this->codeValidTill - time() > 0;
    }

    public function secureCodeAttemptsCount()
    {
        return $this->attemptsCount > 0;
    }

    public function secureCodeCreationAttempt()
    {
        return time() - $this->lastCodeRequestedAt > 60;
    }

}