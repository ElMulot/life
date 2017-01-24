<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Main\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Mvc\I18n\Translator;
use User\Service\AuthManager;
use User\Service\UserManager;
use Main\Controller\IndexController;

/**
 * @package		Main\Controller\Factory
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class IndexControllerFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
	{
		$entityManager = $container->get('doctrine.entitymanager.orm_default');
		$authManager = $container->get(AuthManager::class);
		$userManager = $container->get(UserManager::class);
		$sessionContainer = $container->get('Temp');
		$translator = $container->get(Translator::class);
		$config = $container->get('Config');
        
        return new IndexController($entityManager, $authManager, $userManager, $sessionContainer, $translator, $config);
    }
}