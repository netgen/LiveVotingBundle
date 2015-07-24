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
use Netgen\LiveVotingBundle\Entity\Presentation;
use Netgen\LiveVotingBundle\Entity\PresentationComment;
use Netgen\LiveVotingBundle\Entity\User;
use Netgen\LiveVotingBundle\Exception\JoindInClientException;

/**
 * Class JoindInClient for communicating with joind.in API. Currently supporting Api v2.1
 * @package Netgen\LiveVotingBundle\Service\JoindInClient
 */
class JoindInClient
{

    private $base_url = null;

    /**
     * @var Browser|null
     */
    private $client = null;

    public function __construct($base_url = null, Browser $client = null, $access_token)
    {
        if ($base_url == null) {
            throw new JoindInClientException("Base url not set in config!", null, 500);
        }
        if ($client == null) {
            throw new JoindInClientException("Buzz client not loaded!", null, 500);
        }
        $this->base_url = $base_url;
        $this->api_key = $access_token;
        $this->client = $client;
    }

    /**
     * Obtains first 20 events from joind.in
     * @return array
     * @throws JoindInClientException
     */
    public function obtainEvents()
    {
        /**
         * @var $client Browser
         * @var $response Response
         */
        try {
            $response = $this->client->get($this->base_url . "/events?format=json");

        } catch (ClientException $e) {
            throw new JoindInClientException($e->getMessage(), $e, $response->getStatusCode());
        }
        return json_decode($response->getContent());
    }

    /**
     * @param $user_id Id of joind.in user hosting events
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
            $response = $this->client->get($this->base_url . "/users/" . $user_id . "/hosted?format=json");
        } catch (ClientException $e) {
            throw new JoindInClientException($e->getMessage(), $e, $response->getStatusCode());
        }
        $eventArray = json_decode($response->getContent());
        if ($convertToNativeModel) {
            $entityArray = array();
            foreach ($eventArray->events as $event) {
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
    private function convertToEvent($event)
    {
        if ($event == null) return new Event();
        /**
         * @var $nativeEvent Event
         */
        $nativeEvent = new Event();
        $nativeEvent->setName($event->name);
        $nativeEvent->setBegin(new \DateTime($event->start_date));
        $nativeEvent->setEnd(new \DateTime($event->end_date));
        $nativeEvent->setJoindInId(substr($event->uri, strrpos($event->uri, "/") + 1));
        return $nativeEvent;
    }

    /**
     * @param $event_id Event whose comments are going to be obtained
     * @param bool $convertToNativeModel convert to native bundle entity(not supported)
     * @return array Joind.in comments objects
     * @throws JoindInClientException In case of error while communicating with joind.in API
     */
    public function obtainEventComments($event_id, $convertToNativeModel = false)
    {
        /**
         * @var $response Response
         */
        $response = null;
        try {
            $response = $this->client->get($this->base_url . "/events/" . $event_id . "/comments?format=json");
        } catch (ClientException $e) {
            throw new JoindInClientException($e->getMessage(), $e, $response->getStatusCode());
        }
        $commentArray = json_decode($response->getContent());
        if ($convertToNativeModel) {
            //TODO: events comments are currently not supported natively
        }
        return $commentArray->comments;
    }

    /**
     * Obtains last 20 joind.in comments for given talk.
     * @param $talk joind.in talk id
     * @param $convertToNative if true
     * it will convert join.in comments objects into native LiveVoting\PresentationComment
     * @return array of Joind.in of PresentationComment -s depending on $convertToNative variable
     * @throws JoindInClientException In case of error while communicating with joind.in API
     */
    public function obtainTalkComments($talk, $convertToNative)
    {
        /**
         * @var $response Response
         */
        $response = null;
        try {
            $response = $this->client->get($this->base_url . "/talks/" . $talk . "/comments?format=json");
        } catch (ClientException $e) {
            throw new JoindInClientException($e->getMessage(), $e, $response->getStatusCode());
        }
        if($response->getStatusCode() != 200) return array();
        $commentsArray = json_decode($response->getContent());
        if ($convertToNative) {
            $entityArray = array();
            foreach ($commentsArray->comments as $comment) {
                array_push($entityArray, $this->convertToPresentationComment($comment));
            }
            return $entityArray;
        }
        return $commentsArray->events;
    }

    /**
     * Converts joind.in comment object into native LiveVoting\PresentationComment object
     * @param $comment joind.in comment object
     * @return PresentationComment
     */
    private function convertToPresentationComment($comment)
    {
        if ($comment == null) return new PresentationComment();
        $presentation_comment = new PresentationComment();
        $presentation_comment->setContent($comment->comment);
        $presentation_comment->setPublished(new \DateTime($comment->created_date));
        $user = new User();
        $user->setUsername($comment->user_display_name);
        $presentation_comment->setUser($user);
        return $presentation_comment;
    }

    /**
     * Publish array of presentations on joind.in
     * under on of the events(determined by event id) hosted by
     * user that generated api key.
     * @param String $event Id of joind.in event which api-key user has hosted
     * @param array $presentations array of Presentation entities
     * @return array
     * @throws JoindInClientException
     */
    public function publishPresentations($event, $presentations = array())
    {
        $presentationsArray = array();
        foreach ($presentations as $presentation) {
            array_push($presentationsArray,
                $this->publishPresentation($event, $presentation));
        }
        return $presentationsArray;
    }

    /**
     * Publish Presentation on joind.in event hosted by
     * user that generated api key.
     * @param String $event id of joind.in event which api-key user has hosted
     * @param Presentation $presentation presentation to be published
     * @return Presentation
     * @throws JoindInClientException
     */
    public function publishPresentation($event, Presentation $presentation = null)
    {
        if ($this->api_key == null) {
            throw new JoindInClientException("Publishing presentations requires
             " . "API-key token to be set in configuration", null, 403);
        }
        if ($presentation == null) return;
        /**
         * @var $response Response
         */
        $response = null;
        try {
            $response = $this->client->post(
                $this->base_url . "/events/" . $event . "/talks",
                array(
                    "Authorization" => "Bearer " . $this->api_key
                ),
                json_encode(array(
                    "talk_title" => $presentation->getPresentationName(),
                    "talk_description" => $presentation->getDescription(),
                    "start_date" => $presentation->getBegin()->format(DATE_ISO8601)
                ))
            );
        } catch (ClientException $e) {
            throw new JoindInClientException($e->getMessage(), $e, 500);
        }
        if($response->isOk()) {
            return $this->convertToPresentation(json_decode($response->getContent())->talks[0]);
        } else {
            throw new JoindInClientException($response->getContent(), null, $response->getStatusCode());
        }
    }

    /**
     * Converts joind.in talk into native Presentation entity
     * @param $presentation joind.in talk
     * @return Presentation native entity
     */
    private function convertToPresentation($presentation)
    {
        if($presentation == null) return new Presentation();
        $nativePresentation = new Presentation();
        $nativePresentation->setBegin(new \DateTime($presentation->start_date));
        $nativePresentation->setJoindInId(
            substr($presentation->uri, strrpos($presentation->uri, "/") + 1));
        $nativePresentation->setDescription($presentation->talk_description);
        $nativePresentation->setPresentationName($presentation->talk_tittle);
        return $nativePresentation;
    }


}