<?php

namespace Netgen\LiveVotingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Presentation
 */
class Presentation
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $presenterName;

    /**
     * @var string
     */
    private $presenterSurname;

    /**
     * @var string
     */
    private $presentationName;

    /**
    * @var text
    */
    private $description;

    /**
     * @var boolean
     */
    private $votingEnabled = false;

    private $joind_in_id = null;

    /**
     * @return null
     */
    public function getJoindInId()
    {
        return $this->joind_in_id;
    }

    /**
     * @param null $joind_in_id
     */
    public function setJoindInId($joind_in_id)
    {
        $this->joind_in_id = $joind_in_id;
    }



    /**
     * @return mixed
     */
    public function getGlobalBrake()
    {
        return $this->globalBrake;
    }

    /**
     * @param mixed $globalBrake
     */
    public function setGlobalBrake($globalBrake)
    {
        $this->globalBrake = $globalBrake;
    }

    /**
     * @var boolean
     */
    private $globalBrake = false;


    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $vote;

    /**
     * @var \Netgen\LiveVotingBundle\Entity\Event
     */
    private $event;
    private $user;

    private $country = '';
    /**
     * @var string
     */
    private $image;

    private $hall;

    private $presentationComments;

    /**
     * Constructor
     */

    public function __construct()
    {
        $this->vote = new \Doctrine\Common\Collections\ArrayCollection();
        $this->presentationComments = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set presenterName
     *
     * @param string $presenterName
     * @return Presentation
     */
    public function setPresenterName($presenterName)
    {
        $this->presenterName = $presenterName;

        return $this;
    }

    /**
     * Get presenterName
     *
     * @return string
     */
    public function getPresenterName()
    {
        return $this->presenterName;
    }

    /**
     * Set presenterSurname
     *
     * @param string $presenterSurname
     * @return Presentation
     */
    public function setPresenterSurname($presenterSurname)
    {
        $this->presenterSurname = $presenterSurname;

        return $this;
    }

    /**
     * Get presenterSurname
     *
     * @return string
     */
    public function getPresenterSurname()
    {
        return $this->presenterSurname;
    }

    /**
     * Set presentationName
     *
     * @param string $presentationName
     * @return Presentation
     */
    public function setPresentationName($presentationName)
    {
        $this->presentationName = $presentationName;

        return $this;
    }

    /**
     * Get presentationName
     *
     * @return string
     */
    public function getPresentationName()
    {
        return $this->presentationName;
    }

    /**
     * Set votingEnabled
     *
     * @param boolean $votingEnabled
     * @return Presentation
     */
    public function setVotingEnabled($votingEnabled)
    {
        $this->votingEnabled = $votingEnabled;

        return $this;
    }

    /**
     * Get votingEnabled
     *
     * @return boolean
     */
    public function getVotingEnabled()
    {
        return $this->votingEnabled;
    }

    /**
     * Add vote
     *
     * @param \Netgen\LiveVotingBundle\Entity\Vote $vote
     * @return Presentation
     */
    public function addVote(\Netgen\LiveVotingBundle\Entity\Vote $vote)
    {
        $this->vote[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param \Netgen\LiveVotingBundle\Entity\Vote $vote
     */
    public function removeVote(\Netgen\LiveVotingBundle\Entity\Vote $vote)
    {
        $this->vote->removeElement($vote);
    }

    /**
     * Get vote
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set event
     *
     * @param \Netgen\LiveVotingBundle\Entity\Event $event
     * @return Presentation
     */
    public function setEvent(\Netgen\LiveVotingBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \Netgen\LiveVotingBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    public function setUser (\Netgen\LiveVotingBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $votes;


    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotes()
    {
        return $this->votes;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }


    public function getCountry()
    {
        return $this->country;
    }

    public function setImage($image = null)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }

    private $begin;
    private $end;

    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function setHall($hall)
    {
        $this->hall = $hall;
    }
    public function getHall()
    {
        return $this->hall;
    }

    public function getAbsolutePath()
    {
        return null === $this->image
            ? null
            : $this->getUploadRootDir().'/'.$this->getImageName();
    }

    public function getWebPath()
    {
        return null === $this->image
            ? null
            : $this->getUploadDir().'/'.$this->getImage();
    }

    public function getHashedImageName(){
        return md5(
            $this->getPresenterName().
            $this->getPresenterSurname().
            $this->getId().
            $this->getPresentationName()
        );
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/bundles/livevoting/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'img/faces';
    }


    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getImage()) {
            return;
        }
        if(is_string($this->getImage())) return;

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $new_name = $this->getHashedImageName().'.'.$this->getImage()->guessExtension();
        $this->getImage()->move(
            $this->getUploadRootDir(),
            $new_name
        );

        // set the path property to the filename where you've saved the file
        $this->image = $new_name;
    }



    /**
     * Set description
     *
     * @param string $description
     * @return Presentation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add presentationComments
     *
     * @param \Netgen\LiveVotingBundle\Entity\PresentationComment $presentationComments
     * @return Presentation
     */
    public function addPresentationComment(\Netgen\LiveVotingBundle\Entity\PresentationComment $presentationComments)
    {
        $this->presentationComments[] = $presentationComments;

        return $this;
    }

    /**
     * Remove presentationComments
     *
     * @param \Netgen\LiveVotingBundle\Entity\PresentationComment $presentationComments
     */
    public function removePresentationComment(\Netgen\LiveVotingBundle\Entity\PresentationComment $presentationComments)
    {
        $this->presentationComments->removeElement($presentationComments);
    }

    /**
     * Get presentationComments
     *
     * @return ArrayCollection
     */
    public function getPresentationComments()
    {
        return $this->presentationComments;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $presentationImages;


    /**
     * Add presentationImages
     *
     * @param \Netgen\LiveVotingBundle\Entity\PresentationImage $presentationImages
     * @return Presentation
     */
    public function addPresentationImage(\Netgen\LiveVotingBundle\Entity\PresentationImage $presentationImages)
    {
        $this->presentationImages[] = $presentationImages;

        return $this;
    }

    /**
     * Remove presentationImages
     *
     * @param \Netgen\LiveVotingBundle\Entity\PresentationImage $presentationImages
     */
    public function removePresentationImage(\Netgen\LiveVotingBundle\Entity\PresentationImage $presentationImages)
    {
        $this->presentationImages->removeElement($presentationImages);
    }

    /**
     * Get presentationImages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPresentationImages()
    {
        return $this->presentationImages;
    }

    public function getBeginTime() {
        return $this->begin->format("H:m");
    }

    public function getEndTime() {
        return $this->end->format("H:m");
    }

}
