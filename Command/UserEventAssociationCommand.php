<?php

namespace Netgen\LiveVotingBundle\Command;

use Netgen\LiveVotingBundle\Entity\UserEventAssociation;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserEventAssociationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('userevent:associate')
            ->setDescription('Associates all users with a predefined Event entity.')
            ->addOption(
                'event-id',
                null,
                InputOption::VALUE_REQUIRED,
                'Event Id of the associated event'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $userEntityRepository = $entityManager->getRepository('LiveVotingBundle:User');
        $userEventAssociationEntityRepository = $entityManager->getRepository('LiveVotingBundle:UserEventAssociation');
        $eventEntityRepository = $entityManager->getRepository('LiveVotingBundle:Event');

        $users = $userEntityRepository->findAll();

        $eventId = $input->getOption('event-id');

        if ($eventId)
        {
            $progress = new ProgressBar($output, count($users));
            $progress->setFormat('verbose');

            $progress->setMessage('Starting user-event association');
            $progress->start();

            $event = $eventEntityRepository->find($eventId);

            foreach ($users as $user)
            {
                $userId = $user->getId();

                $userEventAssociation = $userEventAssociationEntityRepository->findBy(array('user' => $userId, 'event' => $eventId));

                $progress->setMessage('Associating...');
                $progress->advance();

                if (count($userEventAssociation) == 0)
                {
                    $userEventAssociation = new UserEventAssociation();

                    $userEventAssociation->setEvent($event);
                    $userEventAssociation->setUser($user);

                    $entityManager->persist($userEventAssociation);
                }
                else
                {
                    continue;
                }
            }

            $progress->setMessage('Finished!');
            $progress->finish();

            $entityManager->flush();
        }
        else
        {
            $output->writeln('You need to provide the --event-id parameter');
        }


    }


}