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
use Doctrine\Common\Collections\ArrayCollection;
use Blog\Entity\Post;
use Blog\Entity\Comment;

/**
 * @package		User\Entity
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 * @ORM\Entity()
 * @ORM\Table(name="user")
 */
class User
{
	// User status constants
	const STATUS_RETIRED = 	0; // Retired user
	const STATUS_ACTIVE = 	1; // Active user
	const STATUS_ADMIN = 	2; // Admin user
	
	/**
	 * @ORM\Id
	 * @ORM\Column(name="id")
	 * @ORM\GeneratedValue
	 */
	protected $id;
	
	/**
	 * @ORM\Column(name="email")
	 */
	protected $email;
	
	/**
	 * @ORM\Column(name="full_name")
	 */
	protected $fullName;
	
	/**
	 * @ORM\Column(name="password")
	 */
	protected $password;
	
	/**
	 * @ORM\Column(name="language")
	 */
	protected $language;
	
	/**
	 * @ORM\Column(name="status")
	 */
	protected $status;
	
	/**
	 * @ORM\Column(name="date_created")
	 */
	protected $dateCreated;
	
	/**
	 * @ORM\Column(name="pwd_reset_token")
	 */
	protected $passwordResetToken;
	
	/**
	 * @ORM\Column(name="pwd_reset_token_creation_date")
	 */
	protected $passwordResetTokenCreationDate;
	
	/**
	 * @ORM\OneToMany(targetEntity="\User\Entity\Waste", mappedBy="user", cascade={"remove"})
	 */
	protected $wastes;
	
	/**
	 * @ORM\OneToMany(targetEntity="\Blog\Entity\Post", mappedBy="user", cascade={"remove"})
	 */
	protected $posts;
	
	/**
	 * @ORM\OneToMany(targetEntity="\Blog\Entity\Comment", mappedBy="user", cascade={"remove"})
	 */
	protected $comments;
	

	public function __construct()
	{
		$this->dateCreated = date('Y-m-d H:i:s');
		$this->wastes = new ArrayCollection();
		$this->posts = new ArrayCollection();
		$this->comments = new ArrayCollection();
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
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		return $this->fullName;
	}

	/**
	 * @param string $fullName
	 */
	public function setFullName($fullName)
	{
		$this->fullName = $fullName;
	}
	
	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return array Table containing all the different status
	 */
	public static function getStatusList()
	{
		return [
				self::STATUS_RETIRED => 'Banni',
				self::STATUS_ACTIVE => 'Actif',
				self::STATUS_ADMIN => 'Admin'
		];
	}

	/**
	 * @return string Current status as string
	 */
	public function getStatusAsString()
	{
		$list = self::getStatusList();
		if (isset($list[$this->status]))
			return $list[$this->status];
		
		return 'Unknown';
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
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
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
	public function getDateCreated()
	{
		return $this->dateCreated;
	}

	/**
	 * @return string
	 */
	public function getResetPasswordToken()
	{
		return $this->passwordResetToken;
	}

	/**
	 * @param string $token
	 */
	public function setPasswordResetToken($token)
	{
		$this->passwordResetToken = $token;
	}

	/**
	 * @return string
	 */
	public function getPasswordResetTokenCreationDate()
	{
		return $this->passwordResetTokenCreationDate;
	}

	/**
	 * @param string $date
	 */
	public function setPasswordResetTokenCreationDate($date)
	{
		$this->passwordResetTokenCreationDate = $date;
	}

	/**
	 * @return Waste
	 */
	public function getWastes()
	{
		return $this->wastes;
	}
	
	/**
	 * @param Waste $waste
	 */
	public function addWaste(Waste $waste)
	{
		$this->wastes[] = $waste;
	}
	

	/**
	 * @return Post
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
}