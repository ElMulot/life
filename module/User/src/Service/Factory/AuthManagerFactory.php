<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;
use User\Service\AuthManager;

/**
 * @package		User\Service\Factory
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class AuthManagerFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
	{
		$authService = $container->get(AuthenticationService::class);
		$sessionManager = $container->get(SessionManager::class);
		$entityManager = $container->get('doctrine.entitymanager.orm_default');
		
		// Get contents of 'access_filter' config key (the AuthManager service
		// will use this data to determine whether to allow currently logged in user
		// to execute the controller action or not
		$config = $container->get('Config');
		if (isset($config['access_filter']))
			$config = $config['access_filter'];
		else
			$config = [];
                        
        return new AuthManager($authService, $sessionManager, $entityManager, $config);
    }
}
