<?php

namespace Netgen\LiveVotingBundle\Controller;

use Netgen\LiveVotingBundle\Entity\Event;
use Netgen\LiveVotingBundle\Entity\Presentation;
use Netgen\LiveVotingBundle\Entity\Question;
use Netgen\LiveVotingBundle\Entity\User;
use Netgen\LiveVotingBundle\Entity\Vote;
use Netgen\LiveVotingBundle\Entity\Answer;
use Netgen\LiveVotingBundle\Exception\JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller {
    
    // TODO: clean code and add comments.
    public function eventStatusAction(Request $request, $event_id){
        try{
            //$sessionId = $request->getSession()->getId();
            $userT = $this->get('security.context')->getToken()->getUser();
            $userId = $userT->getId();
            $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->find($userId);
            $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);

            // !! throws exception
            if(!$event)
                throw new JsonException(array('error'=>1, 'errorMessage'=>'Non existing event.'));
            $response = $this->get('live_voting.handleRequest')->validateEventStatus($event, $user);

            // fetching presentations for current event
            $presentations = $this->getDoctrine()
                ->getRepository('LiveVotingBundle:Presentation')
                ->findBy(array('event'=>$event));

            $response['presentations']=array();
            // adding presentations to response array
            $em = $this->getDoctrine()->getRepository('LiveVotingBundle:Vote');
            foreach($presentations as $presentation){
                $vote = $em->findOneBy(array('user'=>$user, 'presentation'=>$presentation));
                $rate = 0;
                if($vote)$rate = $vote->getRate();
                $tmp = $this->getPresentationArray($presentation, $rate);
                array_push($response['presentations'], $tmp);
            }
            return new JsonResponse($response);
        }catch(JsonException $exception){
            return new JsonResponse(unserialize($exception->getMessage()));
        }

    }

    protected function getPresentationArray(Presentation $presentation, $rate){
        return array(
            'presenterName' => $presentation->getPresenterName(),
            'presenterSurname' => $presentation->getPresenterSurname(),
            'presentationName' => $presentation->getPresentationName(),
            'votingEnabled' => $presentation->getVotingEnabled(),
            'presentationId' => $presentation->getId(),
            'image' =>  $presentation->getImage() ? $presentation->getWebPath() : 'img/man1.png',
            'presenterRate' => $rate
        );
    }

    public function indexAction(Request $request, $event_id){
        $session = $request->getSession();
        $session->start();
        $session_id = $session->getId();
        $userT = $this->get('security.context')->getToken()->getUser();
        $user_id = $userT->getId();
        //var_dump($user_id); die;
        $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->find($user_id);
/*
        // Saving new user to database
        if($user==null){
            $user = new User();
            $user->setId($session_id);
            $user->setEmail('');
            $user->setPassword('');
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
*/
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
        //var_dump($user_id); die;
        return $this->render('LiveVotingBundle:Index:index.html.twig', 
                array('event' => $event)
            );
    }

    public function eventStatusQuestionAction(Request $request, $event_id){
        try{
            $userT = $this->get('security.context')->getToken()->getUser();
            $userId = $userT->getId();
            $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->find($userId);
            $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);

            // !! throws exception
            if(!$event)
                throw new JsonException(array('error'=>1, 'errorMessage'=>'Non existing event.'));
            $response = $this->get('live_voting.handleRequest')->validateEventStatus($event, $user);

            $questions = $this->getDoctrine()
                ->getRepository('LiveVotingBundle:Question')
                ->findBy(array('event'=>$event));

            $response['questions'] = array();

            $em = $this->getDoctrine()->getRepository('LiveVotingBundle:Answer');
            foreach($questions as $question){
                $answer = $em->findOneBy(array('user' => $user, 'question' => $question));
                $rate = 0;
                if($answer)
                    $rate = $answer->getRate();
                $tmp = $this->getQuestionArray($question, $rate);
                array_push($response['questions'], $tmp);
            }
            return new JsonResponse($response);
        }catch(JsonException $exception){
            return new JsonResponse(unserialize($exception->getMessage()));
        }

    }
    protected function getQuestionArray(Question $question, $rate){
        return array(
            'question' => $question->getQuestion(),
            'question_type' => $question->getQuestionType(),
            'votingEnabled' => $question->getVotingEnabled(),
            'questionId' => $question->getId(),            
        );
    }

    public function indexAnswerAction(Request $request, $event_id){
        $session = $request->getSession();
        $session->start();
        $session_id = $session->getId();
        $userT = $this->get('security.context')->getToken()->getUser();
        $user_id = $userT->getId();
        //var_dump($user_id); die;
        $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->find($user_id);

        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
        return $this->render('LiveVotingBundle:Answer:index.html.twig', 
                array('event' => $event)
            );
    }

} 