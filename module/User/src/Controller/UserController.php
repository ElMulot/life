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

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\Result;
use User\Entity\User;
use User\Form\UserForm;
use User\Form\PasswordChangeForm;
use User\Form\PasswordResetForm;

/**
 * @package		User\Controller
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
		$user = $this->entityManager->getRepository(User::class)
			->findOneByEmail($this->Identity()
			->getEmail());
		$form = new UserForm($this->translator, $this->config, 'update', $this->entityManager, $user);
		
		if ($this->getRequest()
			->isPost())
		{
			
			$data = $this->params()
				->fromPost();
			
			$form->setData($data);
			
			if ($form->isValid())
			{
				$data = $form->getData();
				$this->userManager->update($data, $user, 'user');
				$this->Identity()->setLanguage($data['language']);
			}
		}
		else
		{
			$form->setData(array(
					'full_name' => $user->getFullName(),
					'email' => $user->getEmail(),
					'language' => $user->getLanguage()
			));
		}
		
		return new ViewModel(array(
				'user' => $user,
				'form' => $form
		));
	}

	public function addAction()
	{
		$form = new UserForm($this->translator, $this->config, 'create', $this->entityManager);
		
		if ($this->getRequest()->isPost())
		{
			$data = $this->params()
				->fromPost();
			
			$form->setData($data);

			if ($form->isValid())
			{

				$data = $form->getData();
				$user = $this->userManager->add($data);
				$result = $this->authManager->login($user->getEmail(), $data['password'], false);

				if ($result->getCode() == Result::SUCCESS)
				{
					return $this->redirect()->toRoute('home', []);
				}
				else
				{
					return $this->redirect()->toRoute('login', []);
				}
			}
		}
		
		return new ViewModel([
				'form' => $form
		]);
	}
	
	public function changePasswordAction()
	{
		$user = $this->entityManager->getRepository(User::class)
			->find($this->Identity()
			->getId());
		
		if ($user == null)
		{
			$this->getResponse()
				->setStatusCode(404);
			return;
		}

		$form = new PasswordChangeForm('change');
		if ($this->getRequest()->isPost())
		{
			$data = $this->params()->fromPost();
			$form->setData($data);

			if ($form->isValid())
			{
				$data = $form->getData();
				if (!$this->userManager->changePassword($user, $data))
				{
					$this->flashMessenger()->addErrorMessage('USER_CHANGE_PASSWORD_ERROR');
					return new ViewModel([
							'user' =>	$user,
							'form' =>	$form
					]);
				}
				$this->flashMessenger()->addSuccessMessage('USER_CHANGE_PASSWORD_SUCCESS');
				return $this->redirect()->toRoute('account', []);
			}
		}
		
		return new ViewModel([
				'user' => $user,
				'form' => $form
		]);
	}

	public function resetPasswordAction()
	{
		$form = new PasswordResetForm();
		
		if ($this->getRequest()->isPost())
		{
			$data = $this->params()->fromPost();
			$form->setData($data);

			if ($form->isValid())
			{
				$user = $this->entityManager->getRepository(User::class)->findOneByEmail($data['email']);
				if ($user != null)
				{
					$this->userManager->generatePasswordResetToken($user);
					return $this->redirect()->toRoute('account', [
							'action' => 'message',
							'id' => 'sent'
					]);
				}
				else
				{
					$this->flashMessenger()->addErrorMessage('USER_RESET_PASSWORD_INVALID_PASSWORD');
					return new ViewModel([
							'user' =>	$user,
							'form' =>	$form
					]);
				}
			}
		}
		
		return new ViewModel([
				'form' => $form
		]);
	}

	public function messageAction()
	{
		$id = (string) $this->params()->fromRoute('id');
		
		if ($id != 'sent' && $id != 'failed')
		{
			throw new \Exception('Invalid message ID specified');
		}
		
		return new ViewModel([
				'id' => $id
		]);
	}

	public function setPasswordAction()
	{
		$token = $this->params()
			->fromRoute('token', null);
		
		// Validate token length
		if ($token != null && (!is_string($token) || strlen($token) != 32))
		{
			throw new \Exception('Invalid token type or length');
		}
		
		if ($token === null || !$this->userManager->validatePasswordResetToken($token))
		{
			return $this->redirect()
				->toRoute('account', [
					'action' => 'message',
					'id' => 'failed'
			]);
		}
		
		$form = new PasswordChangeForm('reset');
		if ($this->getRequest()->isPost())
		{
			$data = $this->params()->fromPost();	
			$form->setData($data);

			if ($form->isValid())
			{
				
				$data = $form->getData();

				if ($this->userManager->setPasswordByToken($token, $data['password']))
				{
					return $this->redirect()
						->toRoute('account', [
							'action' => 'message',
							'id' => 'set'
					]);
				}
				else
				{
					return $this->redirect()
						->toRoute('account', [
							'action' => 'message',
							'id' => 'failed'
					]);
				}
			}
		}
		
		return new ViewModel([
				'form' => $form
		]);
	}
}


