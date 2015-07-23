<?php

namespace Netgen\LiveVotingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PresentationComment
 */
class PresentationComment
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $content;

    /**
     * @var \DateTime
     */
    private $published;

    /**
     * @var \Netgen\LiveVotingBundle\Entity\Presentation
     */
    private $presentation;

    /**
     * @var \Netgen\LiveVotingBundle\Entity\User
     */
    private $user;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return PresentationComment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set published
     *
     * @param \DateTime $published
     * @return PresentationComment
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set presentation
     *
     * @param \Netgen\LiveVotingBundle\Entity\Presentation $presentation
     * @return PresentationComment
     */
    public function setPresentation(\Netgen\LiveVotingBundle\Entity\Presentation $presentation = null)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * @return \Netgen\LiveVotingBundle\Entity\Presentation
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    public function getUser(){
      return $this->user;
    }

    public function setUser( $user = null){
      $this->user = $user;

      return $this;
    }
}
