<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace User\Service;

use Zend\Authentication\Result;
use User\Entity\User;

/**
 * The AuthManager service is responsible for user's login/logout and simple access 
 * filtering. The access filtering feature checks whether the current visitor 
 * is allowed to see the given page or not.
 * @package		User\Service
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class AuthManager
{
	
	private $authService;
	
	private $sessionManager;

	private $entityManager;

	private $config;

	public function __construct($authService, $sessionManager, $entityManager, $config)
	{
		$this->authService = $authService;
		$this->sessionManager = $sessionManager;
		$this->entityManager = $entityManager;
		$this->config = $config;
	}

	/**
	 * Performs a login attempt. If $rememberMe argument is true, it forces the session
	 * to last for one month (otherwise the session expires on one hour)
	 * @param string $email
	 * @param string $password
	 * @param bool $rememberMe
	 * @return Result
	 */
	public function login($email, $password, $rememberMe)
	{
		// Check if user has already logged in. If so, do not allow to log in
		// twice.
		if ($this->authService->getIdentity() != null)
		{
			throw new \Exception('USER_ALREADY_LOGIN');
		}
		
		// Authenticate with login/password.
		$authAdapter = $this->authService->getAdapter();
		$authAdapter->setEmail($email);
		$authAdapter->setPassword($password);
		$result = $this->authService->authenticate();
		
		// If user wants to "remember him", we will make session to expire in
		// one month. By default session expires in 1 hour (as specified in our
		// config/global.php file).
		if ($result->getCode() == Result::SUCCESS && $rememberMe)
		{
			// Session cookie will expire in 1 month (30 days).
			$this->sessionManager->rememberMe(60 * 60 * 24 * 30);
		}
		
		return $result;
	}

	public function logout()
	{
		// Allow to log out only when user is logged in.
		if ($this->authService->getIdentity() == null)
		{
			throw new \Exception('USER_NOT_LOGIN');
		}
		
		// Remove identity from session.
		$this->authService->clearIdentity();
	}

	/**
	 * This is a simple access control filter. It is able to restrict unauthorized
	 * users to visit certain pages.
	 * 
	 * This method uses the 'access_filter' key in the config file and determines
	 * whenther the current visitor is allowed to access the given controller action
	 * or not. It returns true if allowed; otherwise false.
	 * @param string $controllerName
	 * @param string $actionName
	 * @return bool
	 */
	public function filterAccess($controllerName, $actionName)
	{
		// Determine mode - 'restrictive' (default) or 'permissive'. In restrictive
		// mode all controller actions must be explicitly listed under the 'access_filter'
		// config key, and access is denied to any not listed action for unauthorized users.
		// In permissive mode, if an action is not listed under the 'access_filter' key,
		// access to it is permitted to anyone (even for not logged in users.
		// Restrictive mode is more secure and recommended to use.
		$mode = isset($this->config['options']['mode'])?$this->config['options']['mode']:'restrictive';
		if ($mode != 'restrictive' && $mode != 'permissive')
			throw new \Exception('Invalid access filter mode (expected either restrictive or permissive mode');
		
		if (isset($this->config['controllers'][$controllerName]))
		{
			$items = $this->config['controllers'][$controllerName];
			foreach ($items as $item)
			{
				$actionList = $item['actions'];
				$allow = $item['allow'];
				
				if (is_array($actionList) && in_array($actionName, $actionList) || $actionList == '*')
				{
					if ($allow == '*')
					{
						return true; // Anyone is allowed to see the page.
					} else if ($allow == 'u' && $this->authService->hasIdentity())
					{
						return true; // Only authenticated user is allowed to see the page.
					} else if ($allow == 'a' && $this->authService->hasIdentity()) {
						if ($this->authService->getIdentity()->getStatus() == User::STATUS_ADMIN) {
						return true; // Only admin user is allowed to see the page.
						}
					}
				}
			}
		}
		
		// In restrictive mode, we forbid access for unauthorized users to any
		// action not listed under 'access_filter' key (for security reasons).
		if ($mode == 'restrictive' && !$this->authService->hasIdentity())
			return false;
        
        // Permit access to this page.
        return true;
    }
}