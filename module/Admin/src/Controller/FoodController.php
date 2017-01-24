<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Entity\Food;
use Admin\Form\FoodForm;

/**
 * @package		Admin\Controller
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class FoodController extends AbstractActionController
{
	private $entityManager;

	private $foodManager;

	private $translator;
	
	private $config;

	public function __construct($entityManager, $foodManager, $translator, $config)
	{
		$this->entityManager = $entityManager;
		$this->foodManager = $foodManager;
		$this->translator = $translator;
		$this->config = $config;
	}

	public function indexAction()
	{
		$foods = $this->entityManager->getRepository(Food::class)->findBy([], ['language' => 'ASC', 'foodName' => 'ASC']);
		return new ViewModel([
				'foods' => $foods
		]);
	}

	public function addAction()
	{
		$form = new FoodForm($this->entityManager, $this->translator, $this->config);
		
		if ($this->getRequest()->isPost())
		{
			
			$data = $this->params()->fromPost();

			$form->setData($data);
			
			if ($form->isValid())
			{
				$data = $form->getData();
				$this->foodManager->add($data);
				return $this->redirect()->toRoute('admin/food');
			}
		}

		return new ViewModel([
				'form' => $form
		]);
	}
	
	public function editAction()
	{
		$form = new FoodForm($this->entityManager, $this->translator, $this->config);
		$foodId = (int)$this->params()->fromRoute('id', -1);
		
		if ($foodId<0) {
			$this->getResponse()->setStatusCode(404);
			return;
		}
		
		$food = $this->entityManager->getRepository(Food::class)->findOneById($foodId);
		if ($food == null) {
			$this->getResponse()->setStatusCode(404);
			return;
		}
		
		if ($this->getRequest()->isPost()) {
			$data = $this->params()->fromPost();
		
			$form->setData($data);
			if ($form->isValid()) {
		
				$data = $form->getData();
				$this->foodManager->update($data, $food);
				return $this->redirect()->toRoute('admin/food');
			}
		} else {
			$data = [
					'language' => $food->getLanguage(),
					'food_name' => $food->getFoodName(),
					'weight' => $food->getWeight(),
					'comment' => $food->getComment()
			];
		
			$form->setData($data);
		}

		return new ViewModel([
				'form' => $form,
				'food' => $food
		]);
	}
	
	public function deleteAction()
	{
		$foodId = (int)$this->params()->fromRoute('id', -1);
	
		if ($foodId<0) {
			$this->getResponse()->setStatusCode(404);
			return;
		}
	
		$food = $this->entityManager->getRepository(Food::class)->findOneById($foodId);
		if ($food == null) {
			$this->getResponse()->setStatusCode(404);
			return;
		}
	
		$this->foodManager->delete($food);
		return $this->redirect()->toRoute('admin/food');
	}
}
