<?php

namespace Rudak\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rudak\BlogBundle\Utils\Slug;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table(name="rudakblog_post")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Rudak\BlogBundle\Entity\PostRepository")
 */
class Post
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=150)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=150)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     */
    private $public;

    /**
     * @var integer
     *
     * @ORM\Column(name="hit", type="integer")
     */
    private $hit;

    /**
     * @var integer
     *
     * @ORM\Column(name="creatorId", type="smallint")
     */
    private $creatorId;

    /**
     * @Assert\Valid
     * @var string
     * @ORM\OneToOne(targetEntity="Picture",cascade={"remove","persist"})
     */
    private $picture;


    public function __construct()
    {
        $this->hit    = 0;
        $this->public = false;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function slugTheTitle()
    {
        $Slug = new Slug($this->getTitle());
        $this->setSlug($Slug->getSlug());
    }

    /**
     * @ORM\PrePersist()
     */
    public function setPostDate()
    {
        $this->setDate(new \Datetime);
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
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Post
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
     * Set date
     *
     * @param \DateTime $date
     * @return Post
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set public
     *
     * @param boolean $public
     * @return Post
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Set hit
     *
     * @param integer $hit
     * @return Post
     */
    public function setHit($hit)
    {
        $this->hit = $hit;

        return $this;
    }

    /**
     * Get hit
     *
     * @return integer
     */
    public function getHit()
    {
        return $this->hit;
    }

    /**
     * Set creatorId
     *
     * @param integer $creatorId
     * @return Post
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;

        return $this;
    }

    /**
     * Get creatorId
     *
     * @return integer
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }



    /**
     * Set picture
     *
     * @return Post
     */
    public function setPicture( $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return \Rudak\BlogBundle\Entity\Picture 
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
