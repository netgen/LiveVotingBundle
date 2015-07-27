<?php
/**
 * Created by PhpStorm.
 * User: marin
 * Date: 27.07.15.
 * Time: 12:50
 */

namespace Netgen\LiveVotingBundle\Service\PresentationService\Impl;


use Doctrine\ORM\EntityManager;
use Netgen\LiveVotingBundle\Service\PresentationService\PresentationRepository;
use Netgen\LiveVotingBundle\Service\PresentationService\Record\PresentationRecord;

class DoctrinePresentationRepo implements PresentationRepository {

    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * Method for saving PresentationRecord into some data storage.
     * @param PresentationRecord $presentation object containing presentation data
     * @return PresentationRecord saved presentation object
     */
    public function save(PresentationRecord $presentation)
    {
        // TODO: Implement save() method.
    }

    /**
     * Method for updating PresentationRecord from some storage.
     * @param PresentationRecord $presentation existing presentation
     * @return PresentationRecord update presentation
     */
    public function update(PresentationRecord $presentation)
    {
        // TODO: Implement update() method.
    }

    /**
     * Method for deleting existing presentation from data storage.
     * @param mixed $presentation_id Id of existing presentation
     * @return void
     */
    public function destroy($presentation_id)
    {
        // TODO: Implement destroy() method.
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
        // TODO: Implement find() method.
    }

    /**
     * Method for finding specific presentation by its id.
     * @param mixed $presentation_id
     * @return PresentationRecord Retrieved presentation with given id
     */
    public function findOne($presentation_id)
    {
        // TODO: Implement findOne() method.
    }

    /**
     * Returns all existing presentations.
     * @return array
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
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
        // TODO: Implement vote() method.
    }

    /**
     * Method for retrieving user vote on specific presentation
     * @param mixed $presentation_id Id of presentation
     * @param mixed $user_id Id of user
     * @return mixed user vote or null if user didn't vote this specific presentation
     */
    public function getVote($presentation_id, $user_id)
    {
        // TODO: Implement getVote() method.
    }

    /**
     * Average of user votes on specific presentation.
     * @param mixed $presentation_id Id of presentation
     * @return mixed Average user vote on this presentation
     */
    public function getPresentationRate($presentation_id)
    {
        // TODO: Implement getPresentationRate() method.
    }
}