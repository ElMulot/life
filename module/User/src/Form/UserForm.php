<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * @package		User\Form
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class UserForm extends Form
{

	private $translator;

	private $config;

	private $scenario;

	private $entityManager = null;

	private $user = null;

	public function __construct($translator, $config, $scenario = 'create', $entityManager = null, $user = null)
	{
		parent::__construct('user-form');
		$this->setAttribute('method', 'post');
		$this->translator = $translator;
		$this->config = $config;
		$this->scenario = $scenario;
		$this->entityManager = $entityManager;
		$this->user = $user;
		
		$this->addElements();
		$this->addInputFilter();
	}

	protected function addElements()
	{
		$this->add([
				'type' => 'text',
				'name' => 'email',
				'options' => [
						'label' => 'USER_MAIL'
				]
		]);
		
		$this->add([
				'type' => 'text',
				'name' => 'full_name',
				'options' => [
						'label' => 'USER_FULL_NAME'
				]
		]);
		
		if ($this->scenario == 'create')
		{
			$this->add([
					'type' => 'password',
					'name' => 'password',
					'options' => [
							'label' => 'USER_PASSWORD'
					]
			]);
			
			$this->add([
					'type' => 'password',
					'name' => 'confirm_password',
					'options' => [
							'label' => 'USER_CONFIRM_PASSWORD'
					]
			]);
		}
		elseif ($this->scenario == 'admin')
		{
			$this->add([
					'type' => 'select',
					'name' => 'status',
					'options' => [
							'label' => 'Status',
							'value_options' => [
									0 => 'Banni',
									1 => 'Actif',
									2 => 'Admin'
							]
					],
					'attributes' => [
							'value' => 1
					]
			]);
		}
		
		$this->add([
				'type' => 'select',
				'name' => 'language',
				'options' => [
						'label' => 'LANGUAGE',
						'value_options' => $this->config['application_settings']['aviable_languages']
				],
				'attributes' => [
						'value' => $this->translator->getLocale()
				]
		]);
		
		$this->add([
				'type' => 'submit',
				'name' => 'submit',
				'attributes' => [
						'value' => 'USER_NEW_ACCOUNT'
				]
		]);
	}

	private function addInputFilter()
	{
		$inputFilter = new InputFilter();
		$this->setInputFilter($inputFilter);
		
		$inputFilter->add([
				'name' => 'email',
				'required' => true,
				'filters' => [
						[
								'name' => 'StringTrim'
						]
				],
				'validators' => [
						[
								'name' => 'StringLength',
								'options' => [
										'min' => 1,
										'max' => 128
								]
						],
						[
								'name' => 'EmailAddress',
								'options' => [
										'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
										'useMxCheck' => false
								]
						]
				]
		]);
		
		if ($this->scenario == 'create')
		{
			$inputFilter->add([
					'name' => 'email',
					'validators' =>
					[
							[
									'name' => 'EmailAddress',
									'options' => [
											'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
											'useMxCheck' => false
									]
							]
					]
			]);
		}
		
		$inputFilter->add([
				'name' => 'full_name',
				'required' => true,
				'filters' => [
						[
								'name' => 'StringTrim'
						]
				],
				'validators' => [
						[
								'name' => 'StringLength',
								'options' => [
										'min' => 1,
										'max' => 512
								]
						]
				]
		]);
		
		if ($this->scenario == 'create')
		{
			$inputFilter->add([
					'name' => 'password',
					'required' => true,
					'filters' => [],
					'validators' => [
							[
									'name' => 'StringLength',
									'options' => [
											'min' => 4,
											'max' => 64
									]
							]
					]
			]);
			
			$inputFilter->add([
					'name' => 'confirm_password',
					'required' => true,
					'filters' => [],
					'validators' => [
							[
									'name' => 'Identical',
									'options' => [
											'token' => 'password'
									]
							]
					]
			]);
		}
		elseif ($this->scenario == 'admin')
		{
			$inputFilter->add([
					'name' => 'status',
					'required' => true,
					'filters' => [
							[
									'name' => 'ToInt'
							]
					],
					'validators' => [
							[
									'name' => 'InArray',
									'options' => [
											'haystack' => [
													0,
													1,
													2
											]
									]
							]
					]
			]);
		}
		
		$inputFilter->add([
				'name' => 'language',
				'required' => true,
				'filters' => [
						[
								'name' => 'StringTrim'
						]
				],
				'validators' => [
						[
								'name' => 'InArray',
								'options' => [
										'haystack' => array_keys($this->config['application_settings']['aviable_languages'])
								]
						]
				]
		]);
	}
}