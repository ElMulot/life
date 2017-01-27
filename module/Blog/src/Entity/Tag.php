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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package		Blog\Entity
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 * @ORM\Entity
 * @ORM\Table(name="tag")
 */

class Tag 
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** 
     * @ORM\Column(name="name") 
     */
    protected $name;
    
    /**
     * @ORM\ManyToMany(targetEntity="\Blog\Entity\Post", mappedBy="tags")
     */
    protected $posts;
    
    public function __construct() 
    {        
        $this->posts = new ArrayCollection();        
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
    public function getName() 
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) 
    {
        $this->name = $name;
    }
    
    /**
     * @return Post[]
     */
    public function getPosts() 
    {
        return $this->posts;
    }
    
    /**
     * @param Post $post
     */
    public function addPost(Post $post) 
    {
        $this->posts[] = $post;
	}
	
	/**
	 * @param Post $post
	 */
	public function removePost(Post $post)
	{
		$this->posts->removeElement($post);
	}
}

