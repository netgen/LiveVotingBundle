<?php

namespace Netgen\LiveVotingBundle\Tests\Util;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MemcachePresentationRepositoryTest extends KernelTestCase {

    private $memcachePresentationRepo;

    public function setUp()
    {
        self::bootKernel();
        $this->memcachePresentationRepo = static::$kernel->getContainer()->get('live_voting.memcached_presentation_repo');
    }

    public function testSave() {
        var_dump($this->memcachePresentationRepo);
    }

}