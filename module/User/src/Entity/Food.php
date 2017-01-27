<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Blog\Entity\Post;

/**
 * @package		User\Entity
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 * @ORM\Entity
 * @ORM\Table(name="food")
 */
class Food
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(name="id")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(name="language")
	 */
	protected $language;
	
	/**
	 * @ORM\Column(name="food_name")
	 */
	protected $foodName;

	/**
	 * @ORM\Column(name="weight")
	 */
	protected $weight;
	
	/**
	 * @ORM\Column(name="comment")
	 */
	protected $comment;

	/**
	 * @ORM\ManyToMany(targetEntity="\Blog\Entity\Post")
	 */
	protected $posts;
	
	/**
	 * @ORM\OneToMany(targetEntity="\User\Entity\Waste", mappedBy="food", cascade={"remove"})
	 */
	protected $wastes;
	
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
	public function getFoodName()
	{
		return $this->foodName;
	}

	/**
	 * @param string $foodName
	 */
	public function setFoodName($foodName)
	{
		$this->foodName = $foodName;
	}

	/**
	 * @return int
	 */
	public function getWeight()
	{
		return $this->weight;
	}
	
	/**
	 * @param int $weight
	 */
	public function setWeight($weight)
	{
		$this->weight = $weight;
	}
	
	/**
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * @param string $comment
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
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
}