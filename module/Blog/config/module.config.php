<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Blog;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
		'router' => [
				'routes' => [
						'blog' => [
								'type' => Literal::class,
								'options' => [
										'route' => '/blog',
										'defaults' => [
												'controller' => Controller\IndexController::class,
												'action' => 'index'
										]
								]
						],
						'posts' => [
								'type' => Segment::class,
								'options' => [
										'route' => '/posts[/:action[/:id[/:comment_action[/:comment_id]]]]',
										'constraints' => [
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[0-9]*',
												'comment_action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'comment_id' => '[0-9]*'
										],
										'defaults' => [
												'controller' => Controller\PostController::class,
												'action' => 'admin'
										]
								]
						],
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
												'index'
										],
										'allow' => '*'
								]
						],
						Controller\PostController::class => [
								[
										'actions' => [
												'view',
												'file'
										],
										'allow' => '*'
								],
								[
										'actions' => [
												'add',
												'edit',
												'delete',
												'admin'
										],
										'allow' => 'a'
								]
						]
				]
		],
		
		'controllers' => [
				'factories' => [
						Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
						Controller\PostController::class => Controller\Factory\PostControllerFactory::class,
				]
		],
		
		'service_manager' => [
				'factories' => [
						Service\PostManager::class => Service\Factory\PostManagerFactory::class,
						Service\ImageManager::class => Service\Factory\ImageManagerFactory::class,
						Service\CommentManager::class => Service\Factory\CommentManagerFactory::class,
				]
		],
		
		'session_containers' => [
				'Temp'
		],
		
		'view_manager' => [
				'template_path_stack' => [
						__DIR__ . '/../view'
				]
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
];
