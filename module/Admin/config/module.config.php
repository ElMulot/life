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

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [	
		'router' => [
				'routes' => [
						'admin' => [
								'type' => Literal::class,
								'options' => [
										'route' => '/admin',
										'defaults' => [
												'controller' => Controller\IndexController::class,
												'action' => 'index'
										]
								],
								'may_terminate' => true,
								'child_routes' => [
										'users' => [
												'type' => Segment::class,
												'options' => [
														'route' => '/user[/:action[/:id]]',
														'constraints' => [
																'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
																'id' => '[0-9]*'
														],
														'defaults' => [
																'controller' => Controller\UserController::class,
																'action' => 'index'
														]
												]
										],
										'food' => [
												'type' => Segment::class,
												'options' => [
														'route' => '/food[/:action[/:id]]',
														'constraints' => [
																'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
																'id' => '[0-9]*'
														],
														'defaults' => [
																'controller' => Controller\FoodController::class,
																'action' => 'index'
														]
												]
										]
								]
						],
				],
				
		],
		
		'controllers' => [
				'factories' => [
						Controller\IndexController::class => InvokableFactory::class,
						Controller\UserController::class => Controller\Factory\UserControllerFactory::class,
						Controller\FoodController::class => Controller\Factory\FoodControllerFactory::class,
				]
		],
		
		'service_manager' => [
				'factories' => [
						Service\FoodManager::class => Service\Factory\FoodManagerFactory::class,
				],
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
										],
										'allow' => '*'
								],
						],
				]
		],
		
		'view_manager' => [
				'template_path_stack' => [
						__DIR__ . '/../view'
				]
		]
];
