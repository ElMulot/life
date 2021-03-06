<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Admin;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\I18n\Translator;
use User\Controller\AuthController;
use User\Service\AuthManager;

/**
 * @package		Admin\Controller
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

class Module
{
	public function getConfig()
	{
		return include __DIR__ . '/../config/module.config.php';
	}

	/**
	 * This method is called once the MVC bootstrapping is complete and allows
	 * to register event listeners.
	 * @param MvcEvent $event
	 */
	public function onBootstrap(MvcEvent $event)
	{	
		// Get event manager.
		$eventManager = $event->getApplication()->getEventManager();
		$sharedEventManager = $eventManager->getSharedManager();
		// Register the event listener method.
		$sharedEventManager->attach(AbstractActionController::class, MvcEvent::EVENT_DISPATCH, [
				$this,
				'onDispatch'
		], 100);
	}

	/**
	 * Event listener method for the 'Dispatch' event. We listen to the Dispatch
	 * event to call the access filter. The access filter allows to determine if
	 * the current visitor is allowed to see the page or not. If he/she
	 * is not authorized and is not allowed to see the page, we redirect the user
	 * to the login page.
	 * @param MvcEvent $event
	 * @return Controller
	 */
	public function onDispatch(MvcEvent $event)
	{
		// Get controller and action to which the HTTP request was dispatched.
		$controller = $event->getTarget();
		$controllerName = $event->getRouteMatch()->getParam('controller', null);
		$actionName = $event->getRouteMatch()->getParam('action', null);
		
		// Convert dash-style action name to camel-case.
		$actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
		
		// Get the instance of AuthManager service.
		$authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);
		$authService = $event->getApplication()->getServiceManager()->get(\Zend\Authentication\AuthenticationService::class);
		$translator = $event->getApplication()->getServiceManager()->get(Translator::class);
		
		if ($authService->hasIdentity()) {
			$translator->setLocale($authService->getIdentity()->getLanguage());
		} elseif ($event->getApplication()->getServiceManager()->get('Temp')['locale'] != null) {
			$translator->setLocale($event->getApplication()->getServiceManager()->get('Temp')['locale']);
		} elseif ($event->getApplication()->getServiceManager()->get('Config')['application_settings']['default_language'] != null) {
			$translator->setLocale($event->getApplication()->getServiceManager()->get('Config')['application_settings']['default_language']);
		}
		
		// Execute the access filter on every controller except AuthController
		// (to avoid infinite redirect).
		if ($controllerName != AuthController::class && !$authManager->filterAccess($controllerName, $actionName))
		{
			if ($authService->hasIdentity())
			{ // already login
				return $controller->redirect()->toRoute('home');
			} else
			{
				// Remember the URL of the page the user tried to access. We will
				// redirect the user to that URL after successful login.
				$uri = $event->getApplication()->getRequest()->getUri();
				// Make the URL relative (remove scheme, user info, host name and port)
				// to avoid redirecting to other domain by a malicious user.
				$uri->setScheme(null)->setHost(null)->setPort(null)->setUserInfo(null);
				$redirectUrl = $uri->toString();
		
				if ($redirectUrl != "/")
				{
					// Redirect the user to the "Login" page.
					return $controller->redirect()->toRoute('login', [],
							['query'=>['redirectUrl'=>$redirectUrl]]);
				}
			}
		}
	}
}
