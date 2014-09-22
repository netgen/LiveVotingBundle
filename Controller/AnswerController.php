<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Netgen\LiveVotingBundle\Exception\JsonException;
use Symfony\Component\HttpFoundation\Session\Session;
use Netgen\LiveVotingBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnswerController extends Controller{

public function answerAction(Request $request, $question_id){

        try{
            $rate = $request->request->get('rate');
            $question = $this->getDoctrine()->getRepository('LiveVotingBundle:Question')->find($question_id);
            $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')
                ->find($this->get('security.context')->getToken()->getUser()->getId());
            $event = $question->getEvent();

            $this->get('live_voting.handleRequest')->validateAnswer($question, $event, $user, $rate);
            $answer = $this->getDoctrine()->getRepository('LiveVotingBundle:Answer')->findOneBy(array(
                'user'=>$user,
                'question'=>$question
            ));

            // new answer
            if(!$answer){
                $answer = new Vote();
                $answer->setUser($user);
                $answer->setQuestion($question);
            }
            // saving vote
            $answer->setAnswer($rate);
            $em = $this->getDoctrine()->getManager();
            $em->persist($answer);
            $em->flush();
            $result = array(
                'error' => 0,
                'errorMessage'=>'Thanks for answering!'
            );
            return new JsonResponse($result);
        }catch(JsonException $e){
            return new JsonResponse(unserialize($e->getMessage()));
        }
    }
}