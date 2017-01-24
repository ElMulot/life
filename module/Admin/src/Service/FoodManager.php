<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Admin\Service;

use User\Entity\Food;

/**
 * @package		User\Service
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class FoodManager
{
	
	private $entityManager;
	
	public function __construct($entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	/**
	 * @param array $data
	 */
	public function add($data)
	{
		$food = new Food();
		$food->setLanguage($data['language']);
		$food->setFoodName($data['food_name']);
		$food->setWeight($data['weight']);
		$food->setComment($data['comment']);
		
		$this->entityManager->persist($food);
		$this->entityManager->flush();
	}

	/**
	 * @param array $data
	 * @param Food $food
	 */
	public function update($data, $food)
	{
		$food->setLanguage($data['language']);
		$food->setFoodName($data['food_name']);
		$food->setWeight($data['weight']);
		$food->setComment($data['comment']);
	
		$this->entityManager->persist($food);
		$this->entityManager->flush();
	}

	/**
	 * @param Food $food
	 */
	public function delete($food)
	{
		$this->entityManager->remove($food);
		$this->entityManager->flush();
	}
}