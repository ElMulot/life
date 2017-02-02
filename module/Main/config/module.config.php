<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Main;

use Zend\Router\Http\Literal;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
		'router' => [
				'routes' => [
						'index' => [
								'type' => Literal::class,
								'options' => [
										'route' => '/',
										'defaults' => [
												'controller' => Controller\IndexController::class,
												'action' => 'index'
										]
								],
						],
						'about' => [
								'type' => Literal::class,
								'options' => [
										'route' => '/about',
										'defaults' => [
												'controller' => Controller\IndexController::class,
												'action' => 'about'
										]
								]
						],
						'version' => [
								'type' => Literal::class,
								'options' => [
										'route' => '/version',
										'defaults' => [
												'controller' => Controller\IndexController::class,
												'action' => 'version'
										]
								]
						],
				]
		],
		
		'controllers' => [
				'factories' => [
						Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class
				]
		],
		
		'service_manager' => [
				'factories' => [
						Service\LanguageManager::class => Service\Factory\LanguageManagerFactory::class
				]
		],
		
		'access_filter' => [
				'options' => [
						'mode' => 'restrictive'
				],
				'controllers' => [
						Controller\IndexController::class => [
								[
										'actions' => [
												'index',
												'about',
												'version'
										],
										'allow' => '*'
								]
						]
				]
		],
		
		
		'view_helper_config' => [
				'flashmessenger' => [
						'message_open_format' => '<div%s><ul><li>',
						'message_close_string' => '</li></ul></div>',
						'message_separator_string' => '</li><li>'
				]
		],

		'view_helpers' => [
				'factories' => [
						View\Helper\MessageManager::class => InvokableFactory::class,
				],
				'aliases' => [
						'flashMessenger' => View\Helper\MessageManager::class
				]
		],
		
		'view_manager' => [
				'display_not_found_reason' => true,
				'display_exceptions' => true,
				'doctype' => 'HTML5',
				'not_found_template' => 'error/404',
				'exception_template' => 'error/index',
				'template_map' => [
						'main/layout' => __DIR__ . '/../view/layout/layout.phtml',
						'main/index/index' => __DIR__ . '/../view/main/index/index.phtml',
						'error/404' => __DIR__ . '/../view/error/404.phtml',
						'error/index' => __DIR__ . '/../view/error/index.phtml'
				],
				'template_path_stack' => [
						__DIR__ . '/../view'
				]
		],
];
