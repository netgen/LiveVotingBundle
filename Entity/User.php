<?php

namespace Netgen\LiveVotingBundle\Entity;
   
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Netgen\LiveVotingBundle\Entity\UserRepository")
 */

class User implements UserInterface
{
    /** 
    * @var string
    * @ORM\Column(type="string")
    * @ORM\Id
    * 
    */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="'email'", type="string")
     */

    private $email = null;

    /**
     * 
     * @ORM\Column(type="string")
     * @var string
     */
    private $password = null;

    /**
     * 
     * @ORM\Column(type="boolean")
     * @var enabled
     */
    private $enabled = true;

    /**
     * Set id
     *
     * @param string $id
     * @return User
     */

    private $gender;

    private $country;

    private $city;

    private $tshirt;

    private $foodPreference;

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        //$this->email = $username;
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
    * @inheritDoc
    */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
    * @inheritDoc
    */

    public function getUsername()
    {
        return $this->username;
    }

    /**
    * @inheritDoc
    */

    public function getSalt()
    {
        return '';
    }

    /**
    * @inheritDoc
    */
    public function eraseCredentials()
    {
    }

    /**
     * @var string
     */
    private $username;


    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set tshirt
     *
     * @param string $tshirt
     * @return User
     */
    public function setTshirt($tshirt)
    {
        $this->tshirt = $tshirt;

        return $this;
    }

    /**
     * Get tshirt
     *
     * @return string 
     */
    public function getTshirt()
    {
        return $this->tshirt;
    }

    /**
     * Set foodPreference
     *
     * @param string $foodPreference
     * @return User
     */
    public function setFoodPreference($foodPreference)
    {
        $this->foodPreference = $foodPreference;

        return $this;
    }

    /**
     * Get foodPreference
     *
     * @return string 
     */
    public function getFoodPreference()
    {
        return $this->foodPreference;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $registrations;
    private $presentations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->registrations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->presentations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add registrations
     *
     * @param \Netgen\LiveVotinBundle\Entity\Registration $registrations
     * @return User
     */
    public function addRegistration(\Netgen\LiveVotingBundle\Entity\Registration $registrations)
    {
        $this->registrations[] = $registrations;

        return $this;
    }

    /**
     * Remove registrations
     *
     * @param \Netgen\LiveVotinBundle\Entity\Registration $registrations
     */
    public function removeRegistration(\Netgen\LiveVotingBundle\Entity\Registration $registrations)
    {
        $this->registrations->removeElement($registrations);
    }

    /**
     * Get registrations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRegistrations()
    {
        return $this->registrations;
    }

    public function addPresentation (\Netgen\LiveVotingBundle\Entity\Presentation $presentations)
    {
        $this->presentations[] = $presentations;

        return $this;
    }

    public function removePresentation (\Netgen\LiveVotingBundle\Entity\Presentation $presentations)
    {
        $this->presentations->removeElement($presentations);
    }

    public function getPresentations()
    {
        return $this->presentations;
    }

    public function __toString()
    {
        return $this->email;
    }
}