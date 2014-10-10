<?php

/*
 * This file is part of the Netgen LiveVoting bundle.
 *
 * https://github.com/netgen/LiveVotingBundle
 * 
 */

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Netgen\LiveVotingBundle\Exception\JsonException;
use Symfony\Component\HttpFoundation\Session\Session;
use Netgen\LiveVotingBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Netgen\LiveVotingBundle\Entity\Answer;

/**
 * Answer controller. (user)
 */
class AnswerController extends Controller
{
    /**
     * Returns json response object which contain message
     * @param $request Request
     * @param $question_id Question ID
     * @return $result Returns "Thanks for answering" message
     */
    public function answerAction(Request $request, $question_id)
    {
        try
        {
            $rate = $request->request->get('rate');
            $question = $this->getDoctrine()->getRepository('LiveVotingBundle:Question')->find($question_id);
            $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')
                ->find($this->get('security.context')->getToken()->getUser()->getId());
            $event = $question->getEvent();
            $questionStatus = $question->getVotingEnabled();
            $this->get('live_voting.handleRequest')->validateAnswer($question, $event, $user, $rate, $questionStatus);
            $answer = $this->getDoctrine()->getRepository('LiveVotingBundle:Answer')->findOneBy(array(
                'user'=>$user,
                'question'=>$question
            ));

            // new answer
            if(!$answer){
                $answer = new Answer();
                $answer->setUser($user);
                $answer->setQuestion($question);
            }
            
            // saving answer
            $answer->setAnswer($rate);
            $em = $this->getDoctrine()->getManager();
            $em->persist($answer);
            $em->flush();
            $result = array(
                'error' => 0,
                'errorMessage'=>'Thanks for answering!'
            );
            return new JsonResponse($result);
        }

        catch(JsonException $e)
        {
            return new JsonResponse(unserialize($e->getMessage()));
        }
    }
}