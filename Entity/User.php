<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 14.06.2018
 * Time: 2:14
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements UserInterface, \Serializable
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $password;

    /**
     * @var
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $avatarPath;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActivated;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $middleName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $region;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $district;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $geoTag;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $moderationStatus;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $publicationStatus;

    /**
     * @ORM\Column(type="integer")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isContractor;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    protected $apiToken;

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
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return mixed
     */
    public function getisContractor()
    {
        return $this->isContractor;
    }

    /**
     * @param mixed $isContractor
     */
    public function setIsContractor($isContractor): void
    {
        $this->isContractor = $isContractor;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getAvatarPath()
    {
        return $this->avatarPath;
    }

    /**
     * @param mixed $avatarPath
     */
    public function setAvatarPath($avatarPath): void
    {
        $this->avatarPath = $avatarPath;
    }

    /**
     * @return mixed
     */
    public function getisActivated()
    {
        return $this->isActivated;
    }

    /**
     * @param mixed $isActivated
     */
    public function setIsActivated($isActivated): void
    {
        $this->isActivated = $isActivated;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param mixed $middleName
     */
    public function setMiddleName($middleName): void
    {
        $this->middleName = $middleName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $region
     */
    public function setRegion($region): void
    {
        $this->region = $region;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district): void
    {
        $this->district = $district;
    }

    /**
     * @return mixed
     */
    public function getGeoTag()
    {
        return $this->geoTag;
    }

    /**
     * @param mixed $geoTag
     */
    public function setGeoTag($geoTag): void
    {
        $this->geoTag = $geoTag;
    }

    /**
     * @return mixed
     */
    public function getModerationStatus()
    {
        return $this->moderationStatus;
    }

    /**
     * @param mixed $moderationStatus
     */
    public function setModerationStatus($moderationStatus): void
    {
        $this->moderationStatus = $moderationStatus;
    }

    /**
     * @return mixed
     */
    public function getPublicationStatus()
    {
        return $this->publicationStatus;
    }

    /**
     * @param mixed $publicationStatus
     */
    public function setPublicationStatus($publicationStatus): void
    {
        $this->publicationStatus = $publicationStatus;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param mixed $apiToken
     */
    public function setApiToken($apiToken): void
    {
        $this->apiToken = $apiToken;
    }




}