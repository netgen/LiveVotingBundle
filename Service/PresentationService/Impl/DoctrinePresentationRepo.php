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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException

class DoctrinePresentationRepo implements PresentationRepository {

    private $em;
    private $presentationRepository;
    private $voteRepository;

    public function __construct(EntityManager $em, $presentationRepository, $voteRepository) {
        $this->em = $em;
        $this->presentationRepository = $presentationRepository;
        $this->voteRepository = $voteRepository;
    }

    /**
     * Method for saving PresentationRecord into some data storage.
     * @param PresentationRecord $presentation object containing presentation data
     * @return PresentationRecord saved presentation object
     */
    public function save(PresentationRecord $presentation)
    {
        if($em->getRepository($presentationRepository)->findById($presentation->getId()))
          throw new HttpException(400, 'Presentation id aleady exists.');

        $presentationEntity = $this->presentationObjectToEntity($presentation);
        $em->persist($presentationEntity);
        $em->flush();
        return $presentation;
    }

    /**
     * Method for updating PresentationRecord from some storage.
     * @param PresentationRecord $presentation existing presentation
     * @return PresentationRecord update presentation
     */
    public function update(PresentationRecord $presentation)
    {
      $presentation_id = $presentation->getId();

      $presentationEntity = $em->getRepository($presentationRepository)->findOneById($presentation_id);

      if(!$presentationEntity)
        throw new NotFoundHttpException('Unable to find Presentation entity.');

      $presentationEntity = $this->presentationObjectToEntity($presentation);
      $em->persist($presentationEntity);
      $em->flush();

      return $presentation;
    }

    /**
     * Method for deleting existing presentation from data storage.
     * @param mixed $presentation_id Id of existing presentation
     * @return void
     */
    public function destroy($presentation_id)
    {
        $presentationEntity = $em->getRepository($presentationRepository)->findOneById($presentation_id);

        if(!$presentationEntity)
          throw new NotFoundHttpException('Presentation is already removed.');

        $em->remove($presentationEntity);
        $em->flush();
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
        return $em->getRepository($presentationRepository)->findBy($find_criteria)
              ? $em->getRepository($presentationRepository)->findBy($find_criteria)
              : throw new NotFoundHttpException('Presentation not found.');
    }

    /**
     * Method for finding specific presentation by its id.
     * @param mixed $presentation_id
     * @return PresentationRecord Retrieved presentation with given id
     */
    public function findOne($presentation_id)
    {
          return $em->getRepository($presentationRepository)->findOneById($presentation_id)
                ? $em->getRepository($presentationRepository)->findOneById($presentation_id)
                : throw new NotFoundHttpException('Presentation not found.');
    }

    /**
     * Returns all existing presentations.
     * @return array
     */
    public function findAll()
    {
        return $em->getRepository($presentationRepository)->findAll();
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
        $presentationEntity = $em->getRepository($presentationRepository)->findById($presentation_id);

        if(!$presentationEntity)
          throw new NotFoundHttpException('Presentation not found.');

        $user = $em->getRepository('LiveVotingBundle:User')->findOneById($user_id);

        $vote = $em->getRepository($presentationRepository)->findOneBy(array(
          'id' => $presentation_id,
          'user' => $user
        ));

        return $vote ? $vote->getRate() : null;
    }

    /**
     * Average of user votes on specific presentation.
     * @param mixed $presentation_id Id of presentation
     * @return mixed Average user vote on this presentation
     */
    public function getPresentationRate($presentation_id)
    {
      $presentationEntity = $em->getRepository($presentationRepository)->findById($presentation_id);

      if(!$presentationEntity)
        throw new NotFoundHttpException('Presentation not found.');

      $votes = $presentationEntity->getVotes();

      if(count($votes) > 0){
        $numOfVotes = 0;
        $average = 0;
        foreach ($votes as $vote) {
          $sum += $vote->getRate();
          $numOfVotes++;
        }
        return $sum/$numOfVotes;
      }

      return null;

    }

    public function presentationObjectToEntity(PresentationRecord $presentation){
      $presentationEntity = new Presentation();

      $presentationEntity->setPresentationName($presentation->getName());
      $presentationEntity->setDescription($presentation->getDescription());
      $presentationEntity->setVotingEnabled($presentation->getVotingEnabled());
      $presentationEntity->setGlobalBrake($presentation->getGlobalBrake());
      $presentationEntity->setHall($presentation->getHall());
      $presentationEntity->setBeginTime($presentation->getBegin());
      $presentationEntity->setEndtime($presentation->getEnd());
      $presentationEntity->setJoindInId($presentation->getJoindInId());
      $presentationEntity->setPath($presentation->getImageUrl());

      $event = $em->getRepository('LiveVotingBundle:Event')->findOneById($presentation->getId());

      if($event)
        $presentationEntity->setEvent($event);

      $user = $em->getRepository('LiveVotingBundle:User')->findOneById($presentation->getUserId());

      if($user)
        $presentationEntity->setUser($user);

      return $presentationEntity;
    }

    public function presentationEntityToObject(Presentation $presentationEntity){
      $presentation = new PresentationRecord();

      $presentation->setName($presentationEntity->getPresentationName());
      $presentation->setDescription($presentationEntity->getDescription());
      $presentation->setVotingEnabled($presentationEntity->getVotingEnabled());
      $presentation->setGlobalBrake($presentationEntity->getGlobalBrake());
      $presentation->setHall($presentationEntity->getHall());
      $presentation->setBegin($presentationEntity->getBeginTime());
      $presentation->setEnd($presentationEntity->getEndTime());
      $presentation->setJoindInId($presentationEntity->getJoindInId());
      $presentation->setImageUrl($presentationEntity->getPath());

      $presentation->setEventId($presentationEntity->getEvent()->getId())
      $presentation->setUserId($presentationEntity->getUser()->getId())

      return $presentation;
    }
}
