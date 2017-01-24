<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Entity\Waste;
use User\Entity\User;

/**
 * @package		User\Controller
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class IndexController extends AbstractActionController
{
	
	private $entityManager;

	private $authManager;

	private $userManager;
	
	public function __construct($entityManager, $authManager, $userManager)
	{
		$this->entityManager = $entityManager;
		$this->authManager = $authManager;
		$this->userManager = $userManager;
	}

	public function homeAction()
	{
		$user = $this->entityManager->getRepository(User::class)->findOneById($this->Identity()->getId());
		$wastes = $this->entityManager->getRepository(Waste::class)->findBy(['user' => $user]);
		
		$weightsByDay = []; //Table containing quantities group by day and limit to 10 days
		$weightsByFood = []; //Table containing quantities group by food and limit to 5 days
		
		foreach ($wastes as $waste)
		{
			if ($waste->getUOM() == 'UNIT')
				$weight = $waste->getQuantity() * $waste->getFood()->getWeight();
			else
				$weight = $waste->getQuantity() * 1000;
						
			if (count($weightsByDay) < 10)
			{			
				if (isset($weights[$waste->getDateCreated(true)]))
					$weightsByDay[$waste->getDateCreated(true)] += $weight;
				else 
					$weightsByDay[$waste->getDateCreated(true)] = $weight;
			}
			
			if (isset($overview[$waste->getFood()->getFoodName()]))
				$weightsByFood[$waste->getFood()->getFoodName()] += $weight;
			else
				$weightsByFood[$waste->getFood()->getFoodName()] = $weight;
		}
		
		arsort($weightsByFood);
		$weightsByFoodFiltered['Others'] = 0; //additional key for other foods
		
		$i = 0;
		foreach ($weightsByFood as $key => $weight)
		{
			if ($i < 5)
			{
				$weightsByFoodFiltered[$key] = $weight;
			} else {
				$weightsByFoodFiltered['Others'] += $weight;
			}
			$i++;
		}
		
		return new ViewModel([
				'weightsByDay' => $weightsByDay,
				'weightsByFoodFiltered' => $weightsByFoodFiltered
		]);
	}
}
