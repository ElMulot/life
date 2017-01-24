<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace ZF3;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

/**
 * @package		ZF3
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class Module {
	
	public function getConfig()
	{
		return include __DIR__ . '/../config/module.config.php';
	}
	
	/**
	 * This method is called once the MVC bootstrapping is complete and allows
	 * to register event listeners.
	 * @param MvcEvent $event
	 */
    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $ViewHelperManager=$e->getApplication()->getServiceManager()->get('ViewHelperManager');
        $e->getApplication()->getServiceManager()->get('ViewHelperManager')->setFactory('FlashMsg', function($sm) use ($ViewHelperManager) {
                $viewHelper = new \ZF3\Flashmessenger\View\Helper\FlashMsg(
                    $ViewHelperManager->get('FlashMessenger'),
                    $ViewHelperManager->get('inlinescript'),
                    $ViewHelperManager->get('HeadLink'),
                    $ViewHelperManager->get('url'));
                
                return $viewHelper;
            });
    }

    /*public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__=> __DIR__ . '/src/',
                ),
            ),
        );
    }*/

}
