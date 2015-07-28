<?php
/**
 * Created by PhpStorm.
 * User: marin
 * Date: 27.07.15.
 * Time: 12:51
 */

namespace Netgen\LiveVotingBundle\Service\PresentationService\Impl;


use Exception;
use Memcached;
use Netgen\LiveVotingBundle\Service\PresentationService\PresentationRepository;
use Netgen\LiveVotingBundle\Service\PresentationService\Record\PresentationRecord;

class MemcachedPresentationRepo implements PresentationRepository {

    /**
     * @var Memcached
     */
    private $memcached;

    private $incrementalPresentationId = 1;

    public function __construct() {
        $this->memcached = new Memcached();
        $this->memcached->addServer("localhost", 11211);
    }

    /**
     * Method for saving PresentationRecord into some data storage.
     * @param PresentationRecord $presentation object containing presentation data
     * @return PresentationRecord saved presentation object
     * @throws Exception
     */
    public function save(PresentationRecord $presentation)
    {
        $presentation->setId($this->incrementalPresentationId++);
        if(!$this->memcached->set(
            "presentation-".$presentation->getId(),
            serialize($presentation),
            null
        )) throw new Exception("Error while saving presentation.");
        $presentations = $this->findAll();
        $presentations[$presentation->getId()] = $presentation;
        $this->memcached->set("presentations", serialize($presentations), null);
        return $presentation;
    }

    /**
     * Method for updating PresentationRecord from some storage.
     * @param PresentationRecord $presentation existing presentation
     * @return PresentationRecord update presentation
     * @throws Exception
     */
    public function update(PresentationRecord $presentation)
    {
        if(!$this->memcached->set(
            "presentation-".$presentation->getId(),
            serialize($presentation),
            null
        )) throw new Exception("Error while saving presentation.");
        $presentations = $this->findAll();
        $presentations[$presentation->getId()] = $presentation;
        $this->memcached->set("presentations", serialize($presentations), null);
        return $presentation;
    }

    /**
     * Method for deleting existing presentation from data storage.
     * @param mixed $presentation_id Id of existing presentation
     * @return void
     */
    public function destroy($presentation_id)
    {
        $this->memcached->delete("presentation-".$presentation_id);
        $presentations = $this->findAll();
        unset($presentations[$presentation_id]);
    }

    /**
     * Method for retrieving presentation records from data storage
     * by specific criteria given as key value pair where key would be
     * presentation attribute name and value would be searched value
     * @param array $find_criteria Criteria for finding specific presentations
     * @return array Array of presentations matched by given criteria
     */
    public function find($find_criteria = array())
    {
        $presentations = $this->findAll();
        foreach($find_criteria as $attribute => $value) {
            $presentations = array_filter($presentations, function($presentation) use ($attribute, $value) {
                    if(property_exists($presentation, $attribute)) {
                        if(call_user_func(array($presentation, "get".ucwords($attribute))) != $value) return false;
                    }
                    return true;
            });
        }
        return $presentations;
    }

    /**
     * Method for finding specific presentation by its id.
     * @param mixed $presentation_id
     * @return PresentationRecord Retrieved presentation with given id
     * @throws Exception
     */
    public function findOne($presentation_id)
    {
        $presentation_data = $this->memcached->get("presentation-".$presentation_id);
        if(!$presentation_data) throw new Exception("Presentation with given id does not exist");
        return unserialize($presentation_data);
    }

    /**
     * Returns all existing presentations.
     * @return array
     */
    public function findAll()
    {
        $presentations = $this->memcached->get("presentations");
        if(!$presentations) return array();
        return unserialize($presentations);
    }

    /**
     * Method for giving specific presentation user vote.
     * @param mixed $presentation_id Id of presentation
     * @param mixed $user_id Id of user giving vote
     * @param mixed $vote User given value of presentation (for example 1-5)
     * @return void
     */
    public function vote($presentation_id, $user_id, $vote)
    {
        $votes = $this->getPresentationVotes($presentation_id);
        $votes[$user_id] = $vote;
        $this->memcached->set("votes-".$presentation_id, serialize($votes));
    }

    /**
     * Method for retrieving user vote on specific presentation
     * @param mixed $presentation_id Id of presentation
     * @param mixed $user_id Id of user
     * @return mixed user vote or null if user didn't vote this specific presentation
     */
    public function getVote($presentation_id, $user_id)
    {
        $votes = $this->getPresentationVotes($presentation_id);
        return $votes[$user_id];
    }

    /**
     * Average of user votes on specific presentation.
     * @param mixed $presentation_id Id of presentation
     * @return mixed Average user vote on this presentation
     */
    public function getPresentationRate($presentation_id)
    {
        $votes = $this->getPresentationVotes($presentation_id);
        $total_votes = 0;
        foreach($votes as $vote) {
            $total_votes += $vote;
        }
        return $total_votes/count($votes);
    }

    /**
     * @param $presentation_id
     * @return array|mixed
     */
    private function getPresentationVotes($presentation_id)
    {
        $votesData = $this->memcached->get("votes-" . $presentation_id);
        $votes = array();
        if ($votesData) $votes = unserialize($votesData);
        return $votes;
    }

}