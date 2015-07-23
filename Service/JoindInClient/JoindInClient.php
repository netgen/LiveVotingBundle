<?php
/**
 * Created by PhpStorm.
 * User: marin
 * Date: 23.07.15.
 * Time: 09:54
 */

namespace Netgen\LiveVotingBundle\Service\JoindInClient;


use Buzz\Browser;
use Buzz\Exception\ClientException;
use Buzz\Message\Response;
use Netgen\LiveVotingBundle\Entity\Event;
use Service\JoindInClient\Exception\JoindInClientException;

/**
 * Class JoindInClient for communicating with joind.in API. Currently supporting Api v2.1
 * @package Netgen\LiveVotingBundle\Service\JoindInClient
 */
class JoindInClient {

    private $api_key = null;

    private $base_url = null;

    private $client = null;

    public function __construct($base_url = null, Browser $client = null, $api_key = null)
    {
        if($base_url == null) {
            throw new JoindInClientException("Base url not set in config!", null, 500);
        }
        if($client == null) {
            throw new JoindInClientException("Buzz client not loaded!", null, 500);
        }
        $this->base_url = $base_url;
        $this->api_key = $api_key;
        $this->client = $client;
    }

    /**
     * Obtains first 20 events from joind.in
     * @return array
     */
    public function obtainEvents() {
        /**
         * @var $client Browser
         * @var $response Response
         */
        $response = $this->client->get($this->base_url."/events?format=json");
        return json_decode($response->getContent());
    }

    /**
     * @param $user_id Id or stub of joind.in user hosting events
     * @param bool $convertToNativeModel if true returned events will be
     * converted to native LiveVoting\Event
     * @return array|mixed Array of joind.in events or array of LiveVoting\Event
     * @throws JoindInClientException In case of error while communicating with joind.in API
     */
    public function obtainUserEvents($user_id, $convertToNativeModel = false)
    {
        /**
         * @var $response Response
         */
        $response = null;
        try {
             $response = $this->client->get($this->base_url."/users/".$user_id."/hosted?format=json");
        } catch(ClientException $e) {
            throw new JoindInClientException($e->getMessage(), $e, $response->getStatusCode());
        }
        $eventArray = json_decode($response->getContent());
        if($convertToNativeModel) {
            $entityArray = array();
            foreach($eventArray->events as $event) {
                array_push($entityArray, $this->convertToEvent($event));
            }
            return $entityArray;
        }
        return $eventArray->events;
    }

    /**
     * Method for converting joind.in events into native LiveVoting\Event
     * @param $event joind.in event
     * @return Event converted event
     */
    private function convertToEvent($event) {
        if($event == null) return new Event();
        /**
         * @var $nativeEvent Event
         */
        $nativeEvent = new Event();
        $nativeEvent->setName($event->name);
        $nativeEvent->setBegin(new \DateTime($event->start_date));
        $nativeEvent->setEnd(new \DateTime($event->end_date));
        $nativeEvent->setJoindInId($event->stub);
        return $nativeEvent;
    }

}