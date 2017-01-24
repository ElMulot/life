<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Main\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * @package		Main\Form
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class ContactForm extends Form
{
	
	public function __construct($user = null)
	{
		parent::__construct('user-form');
		$this->setAttribute('method', 'post');
		$this->user = $user;
		
		$this->addElements();
		$this->addInputFilter();
	}

	protected function addElements()
	{	
		$this->add([
				'type' => 'textarea',
				'name' => 'contact',
				'options' => [
						'label' => 'ABOUT_CONTACT_US'
				]
		]);
		
		if ($this->user == null) {
			$this->add([
					'type' => 'text',
					'name' => 'email',
					'options' => [
							'label' => 'USER_MAIL'
					]
			]);
			
			$this->add([
					'type' => 'captcha',
					'name' => 'captcha',
					'options' => [
							'label' => 'Captcha',
							'captcha' => [
									'class' => 'Image',
									'imgDir' => 'public/img/captcha',
									'suffix' => '.png',
									'imgUrl' => '/img/captcha/',
									'imgAlt' => 'CAPTCHA Image',
									'font' => './data/font/thorne_shaded.ttf',
									'fsize' => 24,
									'width' => 350,
									'height' => 100,
									'expiration' => 600,
									'dotNoiseLevel' => 40,
									'lineNoiseLevel' => 3
							],
					],
			]);
			
			$this->add([
					'type' => 'csrf',
					'name' => 'csrf',
					'options' => [
							'csrf_options' => [
									'timeout' => 600
							]
					],
			]);
		}
		
		$this->add([
				'type' => 'submit',
				'name' => 'submit',
				'attributes' => [
						'value' => 'BLOG_POST',
						'id' => 'submit'
				]
		]);
	}

	private function addInputFilter()
	{
		$inputFilter = new InputFilter();
		$this->setInputFilter($inputFilter);
		
		$inputFilter->add([
				'name' => 'contact',
				'required' => true,
				'filters' => [
						[
								'name' => 'StripTags'
						]
				],
				'validators' => [
						[
								'name' => 'StringLength',
								'options' => [
										'min' => 1,
										'max' => 4096
								]
						],
				],
		]);
		
		if ($this->user == null) {
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
									'name' => 'EmailAddress',
									'options' => [
											'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
											'useMxCheck' => false
									]
							]
					]
			]);
		}
    }        
}

