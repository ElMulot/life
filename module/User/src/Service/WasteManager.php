<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace User\Service;

use User\Entity\Waste;
use User\Entity\User;
use User\Entity\Food;

/**
 * @package		User\Service
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class WasteManager
{

	private $entityManager;
	
	public function __construct($entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	/**
	 * @param array $data
	 * @param User $user
	 */
	public function add($data, $user)
	{
		$waste = new Waste();
		$waste->setUser($this->entityManager->getRepository(User::class)->findOneById($user->getId()));
		
		$food = $this->entityManager->getRepository(Food::class)->findOneById($data['food']);
		if ($food == null)
			throw new \Exception('Invalid food name.');
		
		$waste->setFood($food);
		$waste->setQuantity($data['quantity']);
		$waste->setUOM($data['UOM']);
		$waste->setWhy($data['why']);
		
		$this->entityManager->persist($waste);
		$this->entityManager->flush();
	}

	/**
	 * @param array $data
	 * @param Waste $waste
	 */
	public function update($data, $waste)
	{
		$food = $this->entityManager->getRepository(Food::class)->findOneById($data['food']);
		if ($food == null)
			throw new \Exception('No food declarations');
		
		$waste->setFood($food);
		$waste->setQuantity($data['quantity']);
		$waste->setUOM($data['UOM']);
		$waste->setWhy($data['why']);
	
		$this->entityManager->persist($food);
		$this->entityManager->flush();
	}
	
	/**
	 * @param Waste $waste
	 */
	public function delete($waste)
	{	
		$this->entityManager->remove($waste);
		$this->entityManager->flush();
	}
	
}