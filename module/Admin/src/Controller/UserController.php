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
use User\Entity\User;
use User\Form\UserForm;

/**
 * @package		Admin\Controller
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class UserController extends AbstractActionController
{
	private $entityManager;

	private $userManager;

	private $authManager;
	
	private $translator;
	
	private $config;
	
	public function __construct($entityManager, $userManager, $authManager, $translator, $config)
	{
		$this->entityManager = $entityManager;
		$this->userManager = $userManager;
		$this->authManager = $authManager;
		$this->translator = $translator;
		$this->config = $config;
	}

	public function indexAction()
	{
		$users = $this->entityManager->getRepository(User::class)->findBy([], ['id'=>'ASC']);
		
		return new ViewModel([
				'users' => $users
		]);
	}
	
	public function editAction()
	{
		$userId = (int)$this->params()->fromRoute('id', -1);
		
		if ($userId<0) {
			$this->getResponse()->setStatusCode(404);
			return;
		}
		
		$user = $this->entityManager->getRepository(User::class)->findOneById($userId);
		
		$form = new UserForm($this->translator, $this->config, 'admin', $this->entityManager);

		if ($user == null) {
			$this->getResponse()->setStatusCode(404);
			return;
		}
		
		if ($this->getRequest()->isPost())
		{
			$data = $this->params()->fromPost();
			$form->setData($data);
			if ($form->isValid()) {
				$data = $form->getData();
				$this->userManager->update($data, $user, 'admin');
				return $this->redirect()->toRoute('admin/users');
			}
		} else {
			$data = [
					'email' => $user->getEmail(),
					'full_name' => $user->getFullName(),
					'status' => $user->getStatus(),
					'language' => $user->getLanguage(),
			];
		
			$form->setData($data);
		}
		
		return new ViewModel([
				'form' => $form,
				'user' => $user
		]);
	}
		
	public function deleteAction()
	{
		$userId = (int)$this->params()->fromRoute('id', -1);
	
		if ($userId<0) {
			$this->getResponse()->setStatusCode(404);
			return;
		}
	
		$user = $this->entityManager->getRepository(User::class)->findOneById($userId);
		
		if ($user == null) {
			$this->getResponse()->setStatusCode(404);
			return;
		}
	
		$this->userManager->delete($user);
		return $this->redirect()->toRoute('admin/users');
	}
}