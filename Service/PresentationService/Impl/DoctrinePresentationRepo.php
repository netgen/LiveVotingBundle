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
use Netgen\LiveVotingBundle\Entity\Presentation;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        if($this->em->getRepository($this->presentationRepository)->findById($presentation->getId()))
          throw new HttpException(400, 'Presentation id aleady exists.');

        $presentationEntity = $this->presentationObjectToEntity($presentation);
        $this->em->persist($presentationEntity);
        $this->em->flush();
        return $presentationEntity;
    }

    /**
     * Method for updating PresentationRecord from some storage.
     * @param PresentationRecord $presentation existing presentation
     * @return PresentationRecord update presentation
     */
    public function update(PresentationRecord $presentation)
    {
      $presentation_id = $presentation->getId();

      $presentationEntity = $this->em->getRepository($this->presentationRepository)->findOneById($presentation_id);

      if(!$presentationEntity)
        throw new NotFoundHttpException('Unable to find Presentation entity.');

      $presentationEntity = $this->presentationObjectToEntity($presentation, $presentationEntity);
      $this->em->persist($presentationEntity);
      $this->em->flush();

      return $presentationEntity;
    }

    /**
     * Method for deleting existing presentation from data storage.
     * @param mixed $presentation_id Id of existing presentation
     * @return void
     */
    public function destroy($presentation_id)
    {
        $presentationEntity = $this->em->getRepository($this->presentationRepository)->findOneById($presentation_id);

        if(!$presentationEntity)
          throw new NotFoundHttpException('Presentation is already removed.');

        $this->em->remove($presentationEntity);
        $this->em->flush();
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
        $presentationEntity = $this->em->getRepository($this->presentationRepository)->findBy($find_criteria);
              if(!$presentationEntity)
                throw new NotFoundHttpException('Presentation not found.');

              return $presentationEntity;
    }

    /**
     * Method for finding specific presentation by its id.
     * @param mixed $presentation_id
     * @return PresentationRecord Retrieved presentation with given id
     */
    public function findOne($presentation_id)
    {
        $presentationEntity = $this->em->getRepository($this->presentationRepository)->findOneById($presentation_id);

        if(!$presentationEntity)
          throw new NotFoundHttpException('Presentation not found.');

        return $presentationEntity;
    }

    /**
     * Returns all existing presentations.
     * @return array
     */
    public function findAll()
    {
        return $this->em->getRepository($this->presentationRepository)->findAll();
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
        $presentationEntity = $this->em->getRepository($this->presentationRepository)->findOneById($presentation_id);
        if(!$presentationEntity)
          throw new NotFoundHttpException('Presentation not found.');

        $userEntity = $this->em->getRepository('LiveVotingBundle:User')->findOneById($user_id);
        if(!$userEntity)
          throw new NotFoundHttpException('User not found.');

        $voteEntity = $this->em->getRepository('LiveVotingBundle:Vote')->findOneBy(array(
            'user'=>$userEntity,
            'presentation'=>$presentationEntity
        ));

        // new vote
        if(!$voteEntity)
        {
            $voteEntity = new Vote();
            $voteEntity->setUser($userEntity);
            $voteEntity->setPresentation($presentationEntity);
            $voteEntity->setEvent($presentationEntity->getEvent());
        }

        $voteEntity->setRate($vote);

        $this->em->persist($voteEntity);
        $this->em->flush();
    }

    /**
     * Method for retrieving user vote on specific presentation
     * @param mixed $presentation_id Id of presentation
     * @param mixed $user_id Id of user
     * @return mixed user vote or null if user didn't vote this specific presentation
     */
    public function getVote($presentation_id, $user_id)
    {
        $presentationEntity = $this->em->getRepository($this->presentationRepository)->findOneById($presentation_id);

        if(!$presentationEntity)
          throw new NotFoundHttpException('Presentation not found.');

        $user = $this->em->getRepository('LiveVotingBundle:User')->findOneById($user_id);

        $vote = $this->em->getRepository($this->voteRepository)->findOneBy(array(
          'presentation' => $presentationEntity,
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
      $presentationEntity = $this->em->getRepository($this->presentationRepository)->findOneById($presentation_id);

      if(!$presentationEntity)
        throw new NotFoundHttpException('Presentation not found.');

      $votes = $presentationEntity->getVotes();

      if(count($votes) > 0){
        $numOfVotes = 0;
        $sum = 0;
        foreach ($votes as $vote) {
          $sum += $vote->getRate();
          $numOfVotes++;
        }
        return $sum/$numOfVotes;
      }

      return null;

    }

    public function presentationObjectToEntity(PresentationRecord $presentation, Presentation $presentationEntity = null){
      if($presentationEntity === null){
         $presentationEntity = new Presentation();
      }
      $presentationEntity->setId($presentation->getId());
      $presentationEntity->setPresentationName($presentation->getName());
      $presentationEntity->setDescription($presentation->getDescription());
      $presentationEntity->setVotingEnabled($presentation->isVotingEnabled());
      $presentationEntity->setGlobalBrake($presentation->isGlobalBrake());
      $presentationEntity->setHall($presentation->getHall());
      $presentationEntity->setBegin($presentation->getBegin());
      $presentationEntity->setEnd($presentation->getEnd());
      $presentationEntity->setJoindInId($presentation->getJoindInId());
      $presentationEntity->setImage($presentation->getImageUrl());
      $presentationEntity->setPresenterName('Marijo');
      $presentationEntity->setPresenterSurname('Pavlov');

      $event = $this->em->getRepository('LiveVotingBundle:Event')->findOneById($presentation->getId());

      if($event)
        $presentationEntity->setEvent($event);

      $user = $this->em->getRepository('LiveVotingBundle:User')->findOneById($presentation->getUserId());

      if($user)
        $presentationEntity->setUser($user);

      return $presentationEntity;
    }

    public function presentationEntityToObject(Presentation $presentationEntity){
      $presentation = new PresentationRecord();
      $presentation->setId($presentationEntity->getId());
      $presentation->setName($presentationEntity->getPresentationName());
      $presentation->setDescription($presentationEntity->getDescription());
      $presentation->setVotingEnabled($presentationEntity->getVotingEnabled());
      $presentation->setGlobalBrake($presentationEntity->getGlobalBrake());
      $presentation->setHall($presentationEntity->getHall());
      $presentation->setBegin($presentationEntity->getBegin());
      $presentation->setEnd($presentationEntity->getEnd());
      $presentation->setJoindInId($presentationEntity->getJoindInId());
      $presentation->setImageUrl($presentationEntity->getImage());
      /*
      $presentation->setEventId($presentationEntity->getEvent()->getId());
      $presentation->setUserId($presentationEntity->getUser()->getId());
      */
      return $presentation;
    }
}
