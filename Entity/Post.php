<?php

namespace Rudak\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rudak\Slug\Utils\Slug;
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
     * @ORM\Column(name="title", type="string", length=120)
     */
    private $title;


    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=120)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="hat", type="string", length=255)
     */
    private $hat;

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
     * @ORM\Column(name="creatorName", type="string", length=50)
     */
    private $creatorName;

    /**
     * @Assert\Valid
     * @var string
     * @ORM\OneToOne(targetEntity="Picture",cascade={"remove","persist"})
     */
    private $picture;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean",nullable=true)
     */
    private $locked;


    public function __construct()
    {
        $this->hit    = 0;
        $this->public = false;
        $this->date   = new \DateTime('NOW');
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function slugTheTitle()
    {
        $this->setSlug(Slug::slugit($this->getTitle()));
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
        $this->title = ucfirst($title);

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
     * @return string
     */
    public function getHat()
    {
        return $this->hat;
    }

    /**
     * @param string $hat
     */
    public function setHat($hat)
    {
        $this->hat = $hat;
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
     * Set picture
     *
     * @return Post
     */
    public function setPicture($picture = null)
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

    /**
     * @return int
     */
    public function getCreatorName()
    {
        return $this->creatorName;
    }

    /**
     * @param int $creatorName
     */
    public function setCreatorName($creatorName)
    {
        $this->creatorName = $creatorName;
    }

    public function incrementHit()
    {
        $this->hit++;
    }

    public function getUniqUrl()
    {
        $date = $this->date->format(\DateTime::ATOM);
        $slug = new Slug($this->id . ' ' . $date);
        return $slug->getSlug() . '.html';
    }

    /**
     * @return boolean
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * @param boolean $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

}
