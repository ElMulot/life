<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */


namespace Blog\Entity;

use Doctrine\ORM\Mapping as ORM;
use User\Entity\User;

/**
 * @package		Blog\Entity
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 * @ORM\Entity
 * @ORM\Table(name="comment")
 */

class Comment 
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** 
     * @ORM\Column(name="content")  
     */
    protected $content;
	
    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /** 
     * @ORM\Column(name="guest_name")  
     */
    protected $guestName;
    
    /** 
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;

    /**
     * @ORM\ManyToOne(targetEntity="Blog\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    protected $post;

    
    public function __construct()
    {
    	$this->dateCreated = date('Y-m-d H:i:s');
    }

    /**
     * @return int
     */
    public function getId() 
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) 
    {
        $this->id = $id;
    }
    
    /**
     * @return string
     */
    public function getContent() 
    {
        return $this->content;
    }

    /**
     * @param string $comment
     */
    public function setContent($comment) 
    {
        $this->content = $comment;
    }

    /**
     * @return User
     */
    public function getUser()
    {
    	return $this->user;
    }
    
    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
    	$this->user = $user;
    }
    
    /**
     * @return string
     */
    public function getGuestName() 
    {
        return $this->guestName;
    }

    /**
     * @param string $guestName
     */
    public function setGuestName($guestName) 
    {
        $this->guestName = $guestName;
    }
    
    /**
     * @return string
     */
    public function getAuthor()
    {
    	if ($this->user)
    		return $this->user->getFullName();
    	else 
    		return $this->getGuestName();
    }
    
    /**
     * @return string
     */
    public function getDateCreated() 
    {
        return $this->dateCreated;
    }
    
    /**
     * @return Post
     */
    public function getPost() 
    {
        return $this->post;
    }
    
    /**
     * @param Post $post
     */
    public function setPost(Post $post) 
    {
        $this->post = $post;
        $post->addComment($this);
    }
}

