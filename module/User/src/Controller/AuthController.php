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
use Zend\Uri\Uri;
use Main\Form\LoginForm;
use User\Entity\User;

/**
 * @package		User\Controller
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class AuthController extends AbstractActionController
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

	public function loginAction()
	{
		// Retrieve the redirect URL (if passed). We will redirect the user to this
		// URL after successfull login.
		$redirectUrl = (string) $this->params()->fromQuery('redirectUrl', '');
		if (strlen($redirectUrl) > 2048)
		{
			throw new \Exception("Too long redirectUrl argument passed");
		}
		
		$form = new LoginForm();
		$form->get('redirect_url')->setValue($redirectUrl);
		
		// Store login status.
		$isLoginError = false;
		
		if ($this->getRequest()->isPost())
		{
			$data = $this->params()->fromPost();
			$form->setData($data);
			if ($form->isValid())
			{
				$data = $form->getData();
				
				// Perform login attempt.
				$result = $this->authManager->login($data['email'], $data['password'], $data['remember_me']);
				
				// Check result.
				if ($result->getCode() == Result::SUCCESS)
				{
					
					// Get redirect URL.
					$redirectUrl = $this->params()->fromPost('redirect_url', '');
					
					if (!empty($redirectUrl))
					{
						// The below check is to prevent possible redirect attack
						// (if someone tries to redirect user to another domain).
						$uri = new Uri($redirectUrl);
						if (!$uri->isValid() || $uri->getHost() != null)
							throw new \Exception('Incorrect redirect URL: ' . $redirectUrl);
					}
					
					// If redirect URL is provided, redirect the user to that URL;
					// otherwise redirect to Home page.
					if (empty($redirectUrl))
					{
						return $this->redirect()->toRoute('home');
					} else
					{
						$this->redirect()->toUrl($redirectUrl);
					}
				} else
				{
					$isLoginError = true;
				}
			} else
			{
				$isLoginError = true;
			}
		}
		
		return new ViewModel([
				'form' => $form,
				'isLoginError' => $isLoginError,
				'redirectUrl' => $redirectUrl
		]);
	}

	public function logoutAction()
	{
		$this->authManager->logout();
		return $this->redirect()->toRoute('index');
	}
}
