<?php
/**
 * Created by PhpStorm.
 * User: marin
 * Date: 23.07.15.
 * Time: 09:58
 */

namespace Netgen\LiveVotingBundle\Exception;


use Exception;
use Netgen\LiveVotingBundle\Exception\NetgenLiveVotingBundleExceptionInterface;

class JoindInClientException extends Exception implements NetgenLiveVotingBundleExceptionInterface {

    public function __construct($message = null, Exception $previous = null, $code = 200) {
        parent::__construct($message, $code, $previous);
    }

}