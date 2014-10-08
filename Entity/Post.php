<?php

namespace Rudak\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rudak\BlogBundle\Utils\Slug;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\MappedSuperclass
 */
abstract class Post
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=150)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=155)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    protected $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean", nullable=true)
     */
    protected $public;

    /**
     * @var integer
     *
     * @ORM\Column(name="hit", type="integer", nullable=true)
     */
    protected $hit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="editedAt", type="datetime")
     */
    protected $editedAt;

    /**
     *
     * @ORM\oneToOne(targetEntity="Rudak\BlogBundle\Entity\Picture")
     */
    protected $picture;

    public function __construct()
    {
        $this->hit    = 0;
        $this->public = false;
    }

    public function __toString()
    {
        return $this->getSlug();
    }

    /**
     * Set title
     *
     * @param string $title
     */
    protected function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    protected function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    protected function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set content
     *
     * @param string $content
     */
    protected function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    protected function getContent()
    {
        return $this->content;
    }

    /**
     * Set public
     *
     * @param boolean $public
     */
    protected function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean
     */
    protected function getPublic()
    {
        return $this->public;
    }

    /**
     * Set hit
     *
     * @param integer $hit
     */
    protected function setHit($hit)
    {
        $this->hit = $hit;

        return $this;
    }

    /**
     * Get hit
     *
     * @return integer
     */
    protected function getHit()
    {
        return $this->hit;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Test
     */
    protected function setCreatedAt(\Datetime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    protected function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set editedAt
     *
     * @param \DateTime $editedAt
     * @return Test
     */
    protected function setEditedAt(\Datetime $editedAt)
    {
        $this->editedAt = $editedAt;

        return $this;
    }

    /**
     * Get editedAt
     *
     * @return \DateTime
     */
    protected function getEditedAt()
    {
        return $this->editedAt;
    }


    /**
     * Set picture
     *
     * @param \stdClass $picture
     */
    protected function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return \stdClass
     */
    protected function getPicture()
    {
        return $this->picture;
    }


    /**
     * @param object $creator
     */
    protected function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return user object
     */
    protected function getCreator()
    {
        return $this->creator;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preSlugTheTitle()
    {
        $this->setSlug((new Slug())->setString($this->getTitle())->getSlug());
    }
}
