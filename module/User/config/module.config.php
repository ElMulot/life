<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace User;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;


return [
		'router' => [
				'routes' => [						
						'home' => [
								'type' => 'Literal',
								'options' => [
										'route' => '/user/home',
										'defaults' => [
												'controller' => Controller\IndexController::class,
												'action' => 'home'
										]
								],
						],
						'waste' => [
								'type' => Segment::class,
								'options' => [
										'route' => '/user/waste[/:action[/:id]]',
										'constraints' => [
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[0-9]*'
										],
										'defaults' => [
												'controller' => Controller\WasteController::class,
												'action' => 'index'
										]
								]
						],
						'account' => [
								'type' => Segment::class,
								'options' => [
										'route' => '/user/account[/:action[/:id]]',
										'constraints' => [
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[a-zA-Z][a-zA-Z0-9_-]*'
										],
										'defaults' => [
												'controller' => Controller\UserController::class,
												'action' => 'index'
										]
								]
						],
						'login' => [
								'type' => Literal::class,
								'options' => [
										'route' => '/login',
										'defaults' => [
												'controller' => Controller\AuthController::class,
												'action' => 'login'
										]
								]
						],
						'logout' => [
								'type' => Literal::class,
								'options' => [
										'route' => '/user/logout',
										'defaults' => [
												'controller' => Controller\AuthController::class,
												'action' => 'logout'
										]
								]
						],
						'reset-password' => [
								'type' => Literal::class,
								'options' => [
										'route' => '/user/reset-password',
										'defaults' => [
												'controller' => Controller\UserController::class,
												'action' => 'resetPassword'
										]
								]
						],
						'set-password' => [
								'type' => Literal::class,
								'options' => [
										'route' => '/user/set-password',
										'defaults' => [
												'controller' => Controller\UserController::class,
												'action' => 'setPassword'
										]
								]
						],
				]
		],
		
		'controllers' => [
				'factories' => [
						Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
						Controller\WasteController::class => Controller\Factory\WasteControllerFactory::class,
						Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
						Controller\UserController::class => Controller\Factory\UserControllerFactory::class
				]
		],
		
		'service_manager' => [
				'factories' => [
						Service\WasteManager::class => Service\Factory\WasteManagerFactory::class,
						\Zend\Authentication\AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
						Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
						Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
						Service\UserManager::class => Service\Factory\UserManagerFactory::class,
				],
		],
		
		'access_filter' => [
				'options' => [
						'mode' => 'restrictive'
				],
				'controllers' => [
						Controller\UserController::class => [
								[
										'actions' => [
												'add',
												'resetPassword',
												'setPassword',
												'message',
										],
										'allow' => '*'
								],
								[
										'actions' => [
												'index',
												'changePassword',
										],
										'allow' => 'u'
								],
								[
										'actions' => [
												'view'
										],
										'allow' => 'a'
								]
						],
						Controller\WasteController::class => [
								[
										'actions' => [
												'index',
												'add',
										],
										'allow' => 'u'
								]
						],
						Controller\FoodController::class => [
								[
										'actions' => [
												'index',
												'add',
												'edit',
												'delete',
										],
										'allow' => 'a'
								]
						]
				]
		],
		
		'navigation' => [
				'main' => [
						[
								'label' => 'HOME',
								'route' => 'index'
						],
						[
								'label' => 'BLOG',
								'route' => 'blog'
						],
						[
								'label' => 'ABOUT',
								'route' => 'about'
						]
				],
				
				'user' => [
						[
								'label' => 'SUMMARY',
								'route' => 'home'
						],
						[
								'label' => 'WASTE',
								'route' => 'waste'
						],
						[
								'label' => 'BLOG',
								'route' => 'blog'
						],
						[
								'label' => 'MY_ACCOUNT',
								'route' => 'account'
						],
						[
								'label' => 'ABOUT',
								'route' => 'about'
						],
						[
								'label' => 'LOGOUT',
								'route' => 'logout'
						]
				],

				'admin' => [
						[
								'label' => 'SUMMARY',
								'route' => 'home'
						],
						[
								'label' => 'WASTE',
								'route' => 'waste'
						],
						[
								'label' => 'BLOG',
								'route' => 'blog'
						],
						[
								'label' => 'MY_ACCOUNT',
								'route' => 'account'
						],
						[
								'label' => 'Administration',
								'route' => 'admin',
						],
						
						[
								'label' => 'ABOUT',
								'route' => 'about'
						],
						[
								'label' => 'LOGOUT',
								'route' => 'logout'
						]
				],
		],
		
		'doctrine' => [
				'driver' => [
						__NAMESPACE__ . '_driver' => [
								'class' => AnnotationDriver::class,
								'cache' => 'array',
								'paths' => [
										__DIR__ . '/../src/Entity'
								]
						],
						'orm_default' => [
								'drivers' => [
										__NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
								]
						]
				]
		],
		
		'view_manager' => [
				'template_path_stack' => [
						__DIR__ . '/../view'
				]
		],
];
