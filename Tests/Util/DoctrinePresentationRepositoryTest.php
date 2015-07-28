<?php

namespace Netgen\LiveVotingBundle\Tests\Util;

use Netgen\LiveVotingBundle\Service\PresentationService\Impl\DoctrinePresentationRepo;
use Netgen\LiveVotingBundle\Service\PresentationService\Record\PresentationRecord;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DoctrinePresentationRepositoryTest extends WebTestCase {

    /**
     * @var DoctrinePresentationRepo
     */
    private $doctrinePresentationRepo;

    public function setUp()
    {
        self::bootKernel();
        $this->doctrinePresentationRepo = static::$kernel->getContainer()->get('live_voting.doctrine_presentation_repo');
    }

    public function testSavePresentation(){
      $presentation = $this->createPresentationRecord();
      $presentationEntity = $this->doctrinePresentationRepo->save($presentation);
      assert($presentationEntity == $this->doctrinePresentationRepo->findOne($presentationEntity->getId()),
          "Saved presentation is not equal retrived presentation!");
    }

    public function testUpdatePresentation(){
      $presentation = $this->createPresentationRecord();
      $presentation = $this->updatePresentationRecord($presentation);
      $presentationEntity = $this->doctrinePresentationRepo->update($presentation);
      assert($presentationEntity == $this->doctrinePresentationRepo->findOne($presentation->getId()),
          "Updated presentation is equal retrived presentation!");
    }

    public function testDeletePresentation(){
      try{
        $presentation = $this->doctrinePresentationRepo->destroy(87);
      }catch(NotFoundHttpException $e){
        $this->assertEquals('Presentation is already removed.', $e->getMessage());
      }
    }

    public function testFindPresentation(){
      $presentationEntity = $this->doctrinePresentationRepo->find(array('id' => 55));

      $this->assertEquals($presentationEntity[0], $this->doctrinePresentationRepo->findOne(55));
    }

    public function testFindAll(){
      $found = false;
      $presentation = $this->createPresentationRecord();
      $presentation->setDescription('FindAll test');
      $presentationEntity = $this->doctrinePresentationRepo->save($presentation);
      $allPresentations = $this->doctrinePresentationRepo->findAll();
      if(in_array($presentationEntity, $allPresentations))
        $found = true;
      $this->assertEquals(true, $found);
    }

    public function testGetVote(){
      $vote = $this->doctrinePresentationRepo->getVote(60 ,'1434722354559d33837f1f79.91177322');
      $this->assertEquals(5, $vote);
    }

    public function testGetRate(){
      $rate = $this->doctrinePresentationRepo->getPresentationRate(55);
      $this->assertEquals(5, $rate);
    }

    public function testVote(){
      $this->doctrinePresentationRepo->vote(65, '1434722354559d33837f1f79.91177322', 4);
      $em = static::$kernel->getContainer()->get('doctrine')->getManager();

      $vote = $em->getRepository('LiveVotingBundle:Vote')->findOneById(22);

      $this->assertEquals(4, $vote->getRate());
    }

    /**
     * @return PresentationRecord
     */
    public function createPresentationRecord()
    {
        $presentation = new PresentationRecord();
        $presentation->setDescription("Nije novo");
        $presentation->setBegin(new \DateTime());
        $presentation->setJoindInId("asdasdas");
        $presentation->setEnd(new \DateTime());
        $presentation->setEventId(123123);
        $presentation->setHall("D308");
        $presentation->setImageUrl("asdasdasdasd");
        $presentation->setName("sadasdas");
        $presentation->setUserId(123);
        $presentation->setVotingEnabled(true);
        return $presentation;
    }

    /**
     * @return PresentationRecord
     */
    public function updatePresentationRecord($presentation){
      $presentation->setName('Marijo test');
      $presentation->setId(55);
      return $presentation;
    }

}
