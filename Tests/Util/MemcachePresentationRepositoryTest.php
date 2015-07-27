<?php

namespace Netgen\LiveVotingBundle\Tests\Util;

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

    /**
     * @return PresentationRecord
     */
    private function createPresentationRecord()
    {
        $presentation = new PresentationRecord();
        $presentation->setDescription("asafsdfsdfs");
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

}