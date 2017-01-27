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
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use User\Entity\User;
use User\Entity\Food;

/**
 * @package		Blog\Entity
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 * @ORM\Entity(repositoryClass="\Blog\Repository\PostRepository")
 * @ORM\Table(name="post")
 */
class Post 
{
    // Post status constants.
    const STATUS_DRAFT       = 1; // Draft.
    const STATUS_PUBLISHED   = 2; // Published.

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="language")
     */
    protected $language;
    
    /**
     * @ORM\ManyToOne(targetEntity="\User\Entity\User", inversedBy="post")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /** 
     * @ORM\Column(name="title")
     */
    protected $title;
    
    /**
     * @ORM\Column(name="image")
     */
    protected $image;
    
    /** 
     * @ORM\Column(name="content")  
     */
    protected $content;

    /** 
     * @ORM\Column(name="status")  
     */
    protected $status;

    /**
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;
    
    /**
     * @ORM\OneToMany(targetEntity="\Blog\Entity\Comment", mappedBy="post", cascade={"remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="post_id")
     * @OrderBy({"dateCreated" = "ASC"})
     */
    protected $comments;
	
    /**
     * @ORM\ManyToMany(targetEntity="\User\Entity\Food")
     * @OrderBy({"foodName" = "ASC"})
     */
    protected $foods;
    
    /**
     * @ORM\ManyToMany(targetEntity="\Blog\Entity\Tag", inversedBy="posts")
     */
	protected $tags;

	public function __construct()
	{
		$this->dateCreated = date('Y-m-d H:i:s');
		$this->comments = new ArrayCollection();
		$this->foods = new ArrayCollection();
		$this->tags = new ArrayCollection();
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
     * @return User
     */
    public function getUser()
    {
    	return $this->user;
    }
    
    /**
     * @param object $user
     */
    public function setUser(User $user)
    {
    	$this->user = $user;
    }
    
    /**
     * @return string
     */
    public function getLanguage()
    {
    	return $this->language;
    }
    
    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
    	$this->language = $language;
    }
    
    /**
     * @return string
     */
    public function getTitle() 
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) 
    {
        $this->title = $title;
    }
    
    /**
     * @return string
     */
    public function getImage()
    {
    	return $this->image;
    }
    
    /**
     * @param string $image
     */
    public function setImage($image)
    {
    	if (is_array($image))
			$this->image = basename($image['tmp_name']);
    }

    /**
     * @return string
     */
    public function getContent() 
    {
       return html_entity_decode($this->content); 
    }
    
    /**
     * @return string
     */
    public function getShortContent()
    {
    	$content =preg_replace('#<[^\>]*\>#i', '', html_entity_decode($this->content));
    	
    	$contentArray = explode('.', $content);
    	$str = '';
    	foreach ($contentArray as $content) {
    		if (strlen($str) < 100)
    			$str .= $content.'.';
    	}
    	return $str;
    }
    
    /**
     * @param string $content
     */
    public function setContent($content) 
    {
        $this->content = htmlentities($content);
    }
    
    /**
     * @return int
     */
    public function getStatus()
    {
    	return $this->status;
    }
    
    /**
     * @param int $status
     */
    public function setStatus($status)
    {
    	$this->status = $status;
    }
    
    /**
     * @return string
     */
    public function getDateCreated() 
    {
        return $this->dateCreated;
    }
    
    /**
     * @return Comment[]
     */
    public function getComments() 
    {
        return $this->comments;
    }
    
    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment) 
    {
        $this->comments[] = $comment;
    }
 
    /**
     * @return Food
     */
    public function getFoods()
    {
    	return $this->foods;
    }
    
    /**
     * @param Food $food
     */
    public function addFood(Food $food)
    {
    	$this->foods[] = $food;
    }
    
    /**
     * @return Tag[]
     */
    public function getTags() 
    {
        return $this->tags;
    }      
    
    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag) 
    {
    	$this->tags[] = $tag;
    }

    /**
     * @param Tag $tag
     */
    public function removeTagAssociation(Tag $tag)
    {
    	$this->tags->removeElement($tag);
    	$tag->removePost($this);
    }
}

