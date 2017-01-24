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

/**
 * @package		User\Entity
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="\User\Repository\WasteRepository")
 * @ORM\Table(name="waste")
 */
class Waste
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(name="id")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\User\Entity\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\User\Entity\Food", inversedBy="wastes")
	 * @ORM\JoinColumn(name="food_id", referencedColumnName="id")
	 */
	protected $food;
	
	/**
	 * @ORM\Column(name="quantity")
	 */
	protected $quantity;
	
	/**
	 * @ORM\Column(name="UOM")
	 */
	protected $UOM;
	
	/**
	 * @ORM\Column(name="why")
	 */
	protected $why;

	/**
	 * @ORM\Column(name="date_created")
	 */
	protected $dateCreated;
	
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
	 * @return Food
	 */
	public function getFood()
	{
		return $this->food;
	}

	/**
	 * @param Food $food
	 */
	public function setFood(Food $food)
	{
		$this->food = $food;
	}

	/**
	 * @return int
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}

	/**
	 * @param int $quantity
	 */
	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
	}
	
	/**
	 * @return string
	 */
	public function getUOM()
	{
		return $this->UOM;
	}
	
	/**
	 * @param string $UOM
	 */
	public function setUOM($UOM)
	{
		$this->UOM = $UOM;
	}

	/**
	 * @return string
	 */
	public function getWhy()
	{
		return $this->why;
	}

	/**
	 * @param string $why
	 */
	public function setWhy($why)
	{
		$this->why = $why;
	}

	/**
	 * @return string
	 */
	public function getDateCreated($dayFormat = false)
	{
		if ($dayFormat)
			return date_create($this->dateCreated)->format('Y-m-d');
		else
			return $this->dateCreated;
	}
	
	/**
	 * @return int
	 */
	public function getWeight() {
		if ($this->UOM == 'UNIT')
			return $this->quantity * $this->food->getWeight();
		else
			return $this->quantity * 1000;
	}
}