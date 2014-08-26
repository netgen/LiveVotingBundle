<?php

namespace Netgen\LiveVotingBundle\Entity;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class WebserviceUserProvider implements UserProviderInterface

{
    protected $em;

    public function __construct($em){
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        
        $entity = $this->em->getRepository('LiveVotingBundle:User')->find('131309170653fb1f895d6b60.80127849');

        var_dump($entity); return new User();
    }

    

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Netgen\LiveVotingBundle\Entity\User';
    }
}

?>