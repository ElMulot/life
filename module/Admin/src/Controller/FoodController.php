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
		$language = (string) $this->params()->fromQuery('lang', '');
		if (in_array($language, array_keys($this->config['application_settings']['aviable_languages'])) == false) {
			$language = $this->Identity()->getLanguage();
		}
		
		$foods = $this->entityManager->getRepository(Food::class)->findBy(['language' => $language], ['weight' => 'ASC']);
		return new ViewModel([
				'foods' => $foods,
				'language' => $language,
				'aviableLanguages' => $this->config['application_settings']['aviable_languages'],
		]);
	}

	public function addAction()
	{
		$language = (string) $this->params()->fromQuery('lang', '');
		if (in_array($language, array_keys($this->config['application_settings']['aviable_languages'])) == false) {
			$language = $this->Identity()->getLanguage();
		}
		$form = new FoodForm($this->entityManager, $this->translator, $this->config, $language);
		
		if ($this->getRequest()->isPost())
		{
			
			$data = $this->params()->fromPost();

			$form->setData($data);
			
			if ($form->isValid())
			{
				$data = $form->getData();
				$this->foodManager->add($data);
				return $this->redirect()->toRoute('admin/food', ['action' => 'index'], ['query'=>['lang' => $data['language']]]);
			}
		}

		return new ViewModel([
				'form' => $form
		]);
	}
	
	public function editAction()
	{
		$language = (string) $this->params()->fromQuery('lang', '');
		if (in_array($language, array_keys($this->config['application_settings']['aviable_languages'])) == false) {
			$language = $this->Identity()->getLanguage();
		}
		
		$form = new FoodForm($this->entityManager, $this->translator, $this->config, $language);
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
				return $this->redirect()->toRoute('admin/food', ['action' => 'index'], ['query'=>['lang' => $data['language']]]);
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
		return $this->redirect()->toRoute('admin/food', ['action' => 'index'], ['query'=>['lang' => $food->getLanguage()]]);
	}
}
