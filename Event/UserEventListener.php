<?php

namespace Netgen\LiveVotingBundle\Event;

use Doctrine\ORM\EntityManager;

class UserEventListener
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onEventDelete(UpdateOnEventEvent $event)
    {
        $eventId = $event->getEventId();

        $entityRepository = $this->entityManager->getRepository('LiveVotingBundle:UserEventAssociation');

        $userEventAssociations = $entityRepository->findBy(
            array(
                'eventId' => $eventId
            )
        );

        foreach ($userEventAssociations as $userEventAssociation)
        {
            $this->entityManager->remove($userEventAssociation);
        }
    }

    public function onUserDelete(UpdateOnUserEvent $event)
    {
        $userId = $event->getUserId();

        $entityRepository = $this->entityManager->getRepository('LiveVotingBundle:UserEventAssociation');

        $userEventAssociations = $entityRepository->findBy(
            array(
                'userId' => $userId
            )
        );

        foreach ($userEventAssociations as $userEventAssociation)
        {
            $this->entityManager->remove($userEventAssociation);
        }
    }
}