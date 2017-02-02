<?php

/**
 *
 * @copyright Copyright (c) 2015 - 2016
 * @license https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author Mulot
 * @link http://life.je.gfns.ru/
 * @version 0.1 alpha
 * @since File available since 0.1 alpha
 */
namespace Main\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use User\Entity\User;
use Main\Form\LoginForm;
use Main\Form\LocaleForm;
use Main\Form\ContactForm;
use Blog\Entity\Post;

/**
 *
 * @package Main\Controller
 * @copyright Copyright (c) 2015 - 2016
 * @license https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author Mulot
 * @version 0.1 alpha
 * @since File available since 0.1 alpha
 */
class IndexController extends AbstractActionController
{
	private $entityManager;
	private $authManager;
	private $userManager;
	private $sessionContainer;
	private $translator;
	private $config;
	private $versions;

	public function __construct($entityManager, $authManager, $userManager, $sessionContainer, $translator, $config)
	{
		$this->entityManager = $entityManager;
		$this->authManager = $authManager;
		$this->userManager = $userManager;
		$this->sessionContainer = $sessionContainer;
		$this->translator = $translator;
		$this->config = $config;
		$this->versions = [
				[
						'version' => '0.1.2 beta',
						'date' => '02-02-2017',
						'fixed' => [
								'Change how food tab is displayed in admin mode',
								'If weight=0 for a food, the option \'Unit\' for wastes is not aviable',
								'Add a search field for food in waste form',
								'Calculate weight when adding/editing a waste with \'Unit\' selected',
								'Bugs fix'
						]
				],
				[
						'version' => '0.1.1 beta',
						'date' => '01-27-2017',
						'fixed' => [
								'Modify admin blog interface',
								'Correct some bugs in database managment',
								'Changes on version page'
						]
				],
				[
						'version' => '0.1.0 beta',
						'date' => '01-24-2017',
						'fixed' => [
								'First beta release'
						]
				]
		];
	}

	public function indexAction()
	{
		if ($this->Identity() && !$this->getRequest()->isPost()) // user already logged
		{
			return $this->redirect()->toRoute('home');
		}
		
		// Check if we do not have users in database at all. If so, create the 'Admin' user.
		$this->userManager->createAdminUserIfNotExists();
		
		$loginForm = new LoginForm();
		$localeForm = new LocaleForm($this->sessionContainer, $this->config);
		$isLoginError = false;
		
		if ($this->getRequest()->isPost())
		{
			
			$data = $this->params()->fromPost();
			$loginForm->setData($data);
			$localeForm->setData($data);
			
			if ($localeForm->isValid())
			{
				$this->translator->setLocale($data['language']);
				$this->sessionContainer['locale'] = $data['language'];
			}
			elseif ($loginForm->isValid())
			{
				
				$data = $loginForm->getData();
				$result = $this->authManager->login($data['email'], $data['password'], $data['remember_me']);
				
				if ($result->getCode() == Result::SUCCESS)
				{
					return $this->redirect()->toRoute('home');
				}
				else
				{
					$isLoginError = true;
				}
			}
			else
			{
				$isLoginError = true;
			}
		}
		
		// calculation for saved food counter
		$users = $this->entityManager->getRepository(User::class)->findBy([
				'status' => [
						User::STATUS_ACTIVE,
						User::STATUS_ADMIN
				]
		]);
		
		$savedFood = 0;
		$savedFoodDelay = count($users) * 35000 / 143.5;
		foreach ($users as $user)
		{
			$savedFood += (strtotime('now') - strtotime($user->getDateCreated())) / 3600 / 24 * 35000 / 143.5;
			foreach ($user->getWastes() as $waste)
			{
				if (date_create($waste->getDateCreated())->format('Y-m-d') != date_create('now')->format('Y-m-d'))
				{
					$savedFood -= $waste->getWeight();
				}
				if (date_create($waste->getDateCreated())->format('Y-m-d') == date_create('yesterday')->format('Y-m-d'))
				{
					$savedFoodDelay -= $waste->getWeight();
				}
			}
		}
		
		$savedFoodDelay = round(24 * 3600 / $savedFoodDelay * 1000);
		
		$posts = $this->entityManager->getRepository(Post::class)->findFirstPosts($this->translator->getLocale());
		return new ViewModel([
				'loginForm' => $loginForm,
				'localeForm' => $localeForm,
				'isLoginError' => $isLoginError,
				'posts' => $posts,
				'savedFood' => $savedFood,
				'savedFoodDelay' => $savedFoodDelay
		]);
	}

	public function aboutAction()
	{
		$localeForm = new LocaleForm($this->sessionContainer, $this->config);
		$contactForm = new ContactForm($this->Identity());
		$contactFormError = false;
		if ($this->getRequest()->isPost())
		{
			$data = $this->params()->fromPost();
			$localeForm->setData($data);
			$contactForm->setData($data);
			
			if ($localeForm->isValid())
			{
				$this->translator->setLocale($data['language']);
				$this->sessionContainer['locale'] = $data['language'];
			}
			
			$contactFormError = true;
			if ($contactForm->isValid())
			{
				$data = $contactForm->getData();
				$email = ($data['email'] == '')?$this->Identity()->getEmail():$data['email'];
				$headers = 'From: ' . $email . "\r\n" . 'Reply-To: ' . $email . "\r\n" . 'X-Mailer: PHP/' . phpversion();
				mail('rocodutibet@hotmail.com', 'Message de No Waste Life', $data['contact']);
				$this->flashMessenger()->addSuccessMessage('ABOUT_CONTACT_US_SUCCESS');
				$contactFormError = false;
			}
		}
		
		$version = $this->versions[0]['version'];
		
		return new ViewModel([
				'localeForm' => $localeForm,
				'contactForm' => $contactForm,
				'contactFormError' => $contactFormError,
				'version' => $version,
		]);
	}
	
	public function versionAction()
	{
		return new ViewModel([
			'versions' => $this->versions,
		]);
	}
}


