<?php

namespace Rudak\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="blog_comments")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Rudak\BlogBundle\Entity\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \stdClass
     *
     * @ORM\ManyToOne(
     * targetEntity="Post",
     * inversedBy="comments"
     * )
     */
    private $post;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Rcm\UserBundle\Entity\User")
     */
    private $creator;
    #TODO: remettre ca differemment

    /**
     * @var boolean
     *
     * @ORM\Column(name="isSignaled", type="boolean",nullable=true)
     */
    private $isSignaled;


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
     * Set date
     *
     * @param \DateTime $date
     * @return Comment
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
     * Set content
     *
     * @param string $content
     * @return Comment
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
     * Set post
     *
     * @param \Rudak\BlogBundle\Entity\Post $post
     * @return Comment
     */
    public function setPost(\Rudak\BlogBundle\Entity\Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Rudak\BlogBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set creator
     *
     * @param \Rcm\UserBundle\Entity\User $creator
     * @return Comment
     */
    public function setCreator(\Rcm\UserBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \Rcm\UserBundle\Entity\User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set isSignaled
     *
     * @param boolean $isSignaled
     * @return Comment
     */
    public function setIsSignaled($isSignaled)
    {
        $this->isSignaled = $isSignaled;

        return $this;
    }

    /**
     * Get isSignaled
     *
     * @return boolean
     */
    public function getIsSignaled()
    {
        return $this->isSignaled;
    }

    /**
     * @ORM\PrePersist()
     */
    public function updateDate()
    {
        $this->date = new \Datetime();
    }
}
