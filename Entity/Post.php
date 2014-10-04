<?php

namespace Rudak\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="rudakBlog_post")
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
     * @var \DateTime
     *
     * @ORM\Column(name="createDate", type="datetime")
     */
    private $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastEditDate", type="datetime")
     */
    private $lastEditDate;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=120)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=125)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="creator", type="object")
     */
    private $creator;

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

    public function __construct()
    {
        $this->hit = 0;
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
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Post
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set lastEditDate
     *
     * @param \DateTime $lastEditDate
     * @return Post
     */
    public function setLastEditDate($lastEditDate)
    {
        $this->lastEditDate = $lastEditDate;

        return $this;
    }

    /**
     * Get lastEditDate
     *
     * @return \DateTime
     */
    public function getLastEditDate()
    {
        return $this->lastEditDate;
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
     * Set creator
     *
     * @param \stdClass $creator
     * @return Post
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \stdClass
     */
    public function getCreator()
    {
        return $this->creator;
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
}
