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
use User\Form\WasteForm;
use User\Entity\Food;

/**
 * @package		User\Controller
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class WasteController extends AbstractActionController
{
	private $entityManager;
	
	private $wasteManager;
	
	public function __construct($entityManager, $wasteManager)
	{
		$this->entityManager = $entityManager;
		$this->wasteManager = $wasteManager;
	}

	public function indexAction()
	{
		$wastes = $this->entityManager->getRepository(Waste::class)->findBy(['user' => $this->Identity()], ['dateCreated' => 'DESC']);
		return new ViewModel([
				'wastes' => $wastes,
				'entityManager' => $this->entityManager
		]);
	}

	public function addAction()
	{
		$form = new WasteForm($this->entityManager, $this->Identity());
		$foods = $this->entityManager->getRepository(Food::class)->findBy(['language' => $this->Identity()->getLanguage()]);
		
		if ($this->getRequest()->isPost())
		{
			$data = $this->params()->fromPost();
			$form->setData($data);
			
			if ($form->isValid())
			{

				$data = $form->getData();
				$this->wasteManager->add($data, $this->Identity());
				return $this->redirect()->toRoute('waste');
			}
		}

		return new ViewModel([
				'form' => $form,
				'foods' => $foods
		]);
	}
	
	public function editAction()
	{
		$form = new WasteForm($this->entityManager, $this->Identity());
		$foods = $this->entityManager->getRepository(Food::class)->findBy(['language' => $this->Identity()->getLanguage()]);
		
		$wasteId = (int)$this->params()->fromRoute('id', -1);
	
		if ($wasteId<0) {
			$this->getResponse()->setStatusCode(404);
			return;
		}
	
		$waste = $this->entityManager->getRepository(Waste::class)->findOneById($wasteId);
		if ($waste == null) {
			$this->getResponse()->setStatusCode(404);
			return;
		}
	
		if ($this->getRequest()->isPost()) {
			$data = $this->params()->fromPost();
	
			$form->setData($data);
			if ($form->isValid()) {
	
				$data = $form->getData();
				
				$this->wasteManager->update($data, $waste);
				$this->flashMessenger()->addSuccessMessage("La ligne a été modifiée avec succès.");
				return $this->redirect()->toRoute('waste');
			}
		} else {
			$data = [
					'food' => $waste->getFood()->getId(),
					'quantity' => $waste->getQuantity(),
					'UOM' => $waste->getUOM(),
					'why' => $waste->getWhy()
			];
	
			$form->setData($data);
		}
	
		return new ViewModel([
				'form' => $form,
				'waste' => $waste,
				'foods' => $foods
		]);
	}
	

	public function deleteAction()
	{
		$wasteId = (int)$this->params()->fromRoute('id', -1);
	
		if ($wasteId<0) {
			$this->getResponse()->setStatusCode(404);
			return;
		}
	
		$waste = $this->entityManager->getRepository(Waste::class)->findOneById($wasteId);
		if ($waste == null) {
			$this->getResponse()->setStatusCode(404);
			return;
		}

		$this->wasteManager->delete($waste);
		$this->flashMessenger()->addSuccessMessage("La ligne a été supprimée avec succès.");
		return $this->redirect()->toRoute('waste');
	
	}
}
