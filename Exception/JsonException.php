<?php

namespace Netgen\LiveVotingBundle\Exception;


class JsonException extends \Exception implements NetgenLiveVotingBundleExceptionInterface{

    public function __construct($message = null, \Exception $previous = null, $code = 200){
        parent::__construct(serialize($message), $code, $previous);
    }

} 