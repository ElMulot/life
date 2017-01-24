<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\Validator\RemoteAddr;
use Zend\Session\Validator\HttpUserAgent;
use Zend\I18n\Translator\Translator;

return [
		'application_settings' => [
				'aviable_languages' => [
						'ru_RU' => 'Русский',
						'fr_FR' => 'Français',
						'en_US' => 'English'
				],
				'default_language' => 'fr_FR', //overwrite value in translator/locale
				'image_dir' => 'data/upload/',
				'thumbnail_max_width' => 200,
				'image_max_width' => 400,
				'no_image_path' => 'data/img/no_image.jpg',
		],
		
		'session_config' => [
				// Session cookie will expire in 1 hour.
				'cookie_lifetime' => 60 * 60 * 1,
				// Session data will be stored on server maximum for 30 days.
				'gc_maxlifetime' => 60 * 60 * 24 * 30
		],
		
		'session_manager' => [
				// Session validators (used for security).
				'validators' => [
						RemoteAddr::class,
						HttpUserAgent::class
				]
		],
		
		'session_storage' => [
				'type' => SessionArrayStorage::class
		],
		
		'translator' => [
				'locale' => 'fr_FR',
				'translation_file_patterns' => [
						[
								'type' => 'phparray',
								'base_dir' => getcwd() . '/data/language',
								'pattern' => '%s.php'
						]
				]
		],
		
		'view_helpers' => [
				'invokables' => [
						'translate' => \Zend\I18n\View\Helper\Translate::class
				]
		]
];
