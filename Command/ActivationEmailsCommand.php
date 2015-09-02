<?php

namespace Netgen\LiveVotingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ActivationEmailsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('email:send')
            ->setDescription('Send activation emails to all users')
            ->addOption(
                'activation',
                null,
                InputOption::VALUE_NONE,
                'activation'
            )
            ->addOption(
                'questions',
                null,
                InputOption::VALUE_NONE,
                'Questionnaire'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->getContainer()->get('doctrine')->getRepository('LiveVotingBundle:User')->findAll();
        $num = 0;
        if ($input->getOption('activation'))
        {
            foreach ($users as $user) {
                $user_email = $user->getEmail();
                $emailHash = md5($this->getContainer()->getParameter('email_hash_prefix') . $user_email);

                $message = \Swift_Message::newInstance()
                    ->setSubject('Say hello to Summer Camp 2015!')
                    ->setFrom('info@netgen.hr')
                    ->setTo($user_email)
                    ->setBody(
                        $this->getContainer()->get('templating')->render(
                            'LiveVotingBundle:Email:login.html.twig',
                            array('emailHash' => $emailHash)
                        ),
                        'text/html'
                    );
                $this->getContainer()->get('mailer')->send($message);
                $num++;
            }
            $output->writeln('Activation mails have been sent to '.$num.' users');
        }
        else if ($input->getOption('questions'))
        {
            foreach ($users as $user) {
                $user_email = $user->getEmail();
                $emailHash = md5($this->getContainer()->getParameter('email_hash_prefix') . $user_email);

                $message = \Swift_Message::newInstance()
                    ->setSubject('PHP & eZ Publish Summer Camp 2015 - Questionnaire')
                    ->setFrom('info@netgen.hr')
                    ->setTo($user_email)
                    ->setBody(
                        $this->getContainer()->get('templating')->render(
                            'LiveVotingBundle:Email:questions.html.twig',
                            array('emailHash' => $emailHash)
                        ),
                        'text/html'
                    );
                $this->getContainer()->get('mailer')->send($message);
                $num++;
            }
            $output->writeln('Questionnaire mails have been sent to '.$num.' users');
        }
        else
        {
            $output->writeln('Must choose one optional argument: --activation or --questions');
        }

    }
}
