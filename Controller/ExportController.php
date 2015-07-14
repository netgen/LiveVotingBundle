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

            fputcsv($handle, array('Email','Gender','Country','City','T-Shirt','Food preference'),';');
            //$results = $this->connection->query('SELECT email, gender, country, city, tshirt, foodPreference FROM user');
            //$results->execute();
            foreach ($users as $user) {
                fputcsv($handle, array($user->getEmail(),
                    $user->getGender(),
                    $user->getCountry(),
                    $user->getCity(),
                    $user->getTshirt(),
                    $user->getFoodPreference()
                ),';');
            }
            fclose($handle);

        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition','attachment; filename="export.csv"');

        return $response;
    }
}