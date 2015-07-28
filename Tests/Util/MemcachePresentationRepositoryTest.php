<?php

namespace Netgen\LiveVotingBundle\Tests\Util;

use DateTime;
use Exception;
use Netgen\LiveVotingBundle\Service\PresentationService\Impl\MemcachedPresentationRepo;
use Netgen\LiveVotingBundle\Service\PresentationService\Record\PresentationRecord;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MemcachePresentationRepositoryTest extends WebTestCase {

    /**
     * @var MemcachedPresentationRepo
     */
    private $memcachePresentationRepo;

    public function setUp()
    {
        self::bootKernel();
        $this->memcachePresentationRepo = new MemcachedPresentationRepo();
    }

    public function testSavePresentation() {
        $presentation = $this->createPresentationRecord();
        $presentation = $this->memcachePresentationRepo->save($presentation);
        assert($presentation == $this->memcachePresentationRepo->findOne($presentation->getId()),
            "Saved presentation is not equal retrived presentation!");
    }

    public function testUpdatePresentation() {
        $orginalPresentation = $this->createPresentationRecord();
        $orginalPresentation = $this->memcachePresentationRepo->save($orginalPresentation);
        $newPresentation = $orginalPresentation;
        $newPresentation->setName("Novo Ime");
        $newPresentation = $this->memcachePresentationRepo->update($orginalPresentation);
        $memcachePresentation = $this->memcachePresentationRepo->findOne($newPresentation->getId());
        assert($memcachePresentation == $newPresentation, "Updated presentation doesn't match memcached presentation!");
    }

    /**
     * @expectedException Exception
     */
    public function testDestroyPresentation() {
        $presentation = $this->createPresentationRecord();
        $presentation = $this->memcachePresentationRepo->save($presentation);
        $this->memcachePresentationRepo->destroy($presentation->getId());
        $this->memcachePresentationRepo->findOne($presentation->getId());
    }

    public function testFind() {
        $presentation1 = $this->createPresentationRecord();
        $presentation2 = $this->createPresentationRecord2();
        $presentation1 = $this->memcachePresentationRepo->save($presentation1);
        $presentation2 = $this->memcachePresentationRepo->save($presentation2);
        $expectedArray = array($presentation1->getId() => $presentation1);
        $givenArray = $this->memcachePresentationRepo->find(array("name" => "sadasdas"));
        var_dump($expectedArray);
        var_dump($givenArray);
        assert($expectedArray == $givenArray, "Arrays doesn't match");
    }

    public function testFindAll() {
        $presentation1 = $this->createPresentationRecord();
        $presentation2 = $this->createPresentationRecord();
        $this->memcachePresentationRepo->save($presentation1);
        $this->memcachePresentationRepo->save($presentation2);
        $originalPresentations = array($presentation1->getId() => $presentation1, $presentation2->getId()=>$presentation2);
        $presentations = $this->memcachePresentationRepo->findAll();
        assert($originalPresentations == $presentations, "Original array of presentations doesn't match array from memcached!");
    }

    public function testVote() {
        $presentation = $this->createPresentationRecord();
        $presentation = $this->memcachePresentationRepo->save($presentation);
        $this->memcachePresentationRepo->vote($presentation->getId(), 123, 4);
        $user_vote = $this->memcachePresentationRepo->getVote($presentation->getId(), 123);
        assert($user_vote === 4, "User vote doesn't match saved user vote!");
    }

    public function testGetPresentationRate() {
        $presentation = $this->createPresentationRecord();
        $presentation = $this->memcachePresentationRepo->save($presentation);
        $this->memcachePresentationRepo->vote($presentation->getId(), 123, 4);
        $this->memcachePresentationRepo->vote($presentation->getId(), 124, 5);
        $this->memcachePresentationRepo->vote($presentation->getId(), 125, 3);
        $rate = $this->memcachePresentationRepo->getPresentationRate($presentation->getId());
        assert($rate === 4, "Presentation rate doesn't match presentation rate in memcache");
    }

    /**
     * @return PresentationRecord
     */
    private function createPresentationRecord()
    {
        $presentation = new PresentationRecord();
        $presentation->setDescription("asafsdfsdfs");
        $presentation->setBegin(new DateTime());
        $presentation->setJoindInId("asdasdas");
        $presentation->setEnd(new DateTime());
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
    private function createPresentationRecord2()
    {
        $presentation = new PresentationRecord();
        $presentation->setDescription("acsdfsdfff");
        $presentation->setBegin(new DateTime());
        $presentation->setJoindInId("asdasdas");
        $presentation->setEnd(new DateTime());
        $presentation->setEventId(123123);
        $presentation->setHall("D308");
        $presentation->setImageUrl("asdasdasdasd");
        $presentation->setName("fvfvfvfv");
        $presentation->setUserId(123);
        $presentation->setVotingEnabled(true);
        return $presentation;
    }

}