<?php

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class ValidateRequest{

    protected $request, $em;

    public function __construct(Request $request, EntityManager $em){
        $this->request = $request;
        $this->em = $em;
    }

    public function validateEventStatus($event_id){

    }

}