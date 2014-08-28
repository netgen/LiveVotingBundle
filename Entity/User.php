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
    private $enabled = false;

    /**
     * Set id
     *
     * @param string $id
     * @return User
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
}
