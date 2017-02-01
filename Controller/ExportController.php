<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 7/13/15
 * Time: 4:07 PM
 */

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Netgen\LiveVotingBundle\Entity\User;

class ExportController extends Controller {

    public function generateCsvAction() {

        $response= new StreamedResponse();

        $users = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->findAll();

        $response->setCallback(function() use ($users) {

            $handle = fopen('php://output','w+');

            fputcsv(
                $handle,
                array(
                    'Email',
                    'Events',
                    'Event count'
                ),
                ';'
            );

            foreach ($users as $user) {
                $eventAssociations = $user->getEventAssociations();

                $eventCount = $eventAssociations->count();

                $events = array();

                if ($eventCount > 0) {
                    foreach ($eventAssociations as $eventAssociation)
                    {
                        $events[] = $eventAssociation->getEvent()->getName();
                    }
                }

                fputcsv(
                    $handle,
                    array(
                        $user->getEmail(),
                        implode(', ', $events),
                        $eventCount
                    ),
                    ';'
                );
            }
            fclose($handle);

        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition','attachment; filename="export.csv"');

        return $response;
    }
}
