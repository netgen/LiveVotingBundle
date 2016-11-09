<?php
/**
 * Created by PhpStorm.
 * User: tomislav
 * Date: 09.11.16.
 * Time: 16:30
 */

namespace Netgen\LiveVotingBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class UpdateOnUserEvent extends Event
{
    /**
     * @var string
     */
    private $userId;

    /**
     * UpdateOnUserEvent constructor.
     * @param $userId
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }
}