<?php

namespace Netgen\LiveVotingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @package Netgen\LiveVotingBundle\Entity
 */
class User implements UserInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var null|string
     */
    private $email = null;

    /**
     * @var null|string
     */
    private $password = null;

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $tshirt;

    /**
     * @var string
     */
    private $foodPreference;

    /**
     * @var ArrayCollection
     */
    private $presentationComments;

    /**
     * @var string
     */
    private $gravatar;

    /**
     * @var ArrayCollection
     */
    private $registrations;

    /**
     * @var ArrayCollection
     */
    private $presentations;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $presentationImages;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->presentations = new ArrayCollection();
        $this->presentationComments = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getGravatar()
    {
        return $this->gravatar;
    }

    /**
     * @param mixed $gravatar
     */
    public function setGravatar($gravatar)
    {
        $this->gravatar = $gravatar;
    }

    /**
     * Set id
     *
     * @param $id
     * @return $this
     */
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
     * Add registration
     *
     * @param Registration $registrations
     * @return $this
     */
    public function addRegistration(Registration $registrations)
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

    /**
     * Add presentationComments
     *
     * @param \Netgen\LiveVotingBundle\Entity\PresentationComment $presentationComments
     * @return Presentation
     */
    public function addPresentationComment(\Netgen\LiveVotingBundle\Entity\PresentationComment $presentationComments)
    {
        $this->presentationComments[] = $presentationComments;

        return $this;
    }

    /**
     * Remove presentationComments
     *
     * @param \Netgen\LiveVotingBundle\Entity\PresentationComment $presentationComments
     */
    public function removePresentationComment(\Netgen\LiveVotingBundle\Entity\PresentationComment $presentationComments)
    {
        $this->presentationComments->removeElement($presentationComments);
    }

    /**
     * Get presentationComments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPresentationComments()
    {
        return $this->presentationComments;
    }

    /**
     * Add presentationImages
     *
     * @param PresentationImage $presentationImages
     * @return User
     */
    public function addPresentationImage(PresentationImage $presentationImages)
    {
        $this->presentationImages[] = $presentationImages;

        return $this;
    }

    /**
     * Remove presentationImages
     *
     * @param PresentationImage $presentationImages
     */
    public function removePresentationImage(PresentationImage $presentationImages)
    {
        $this->presentationImages->removeElement($presentationImages);
    }

    /**
     * Get presentationImages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPresentationImages()
    {
        return $this->presentationImages;
    }
}
