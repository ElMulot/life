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
class LoginForm extends Form
{
	
	public function __construct()
	{
		parent::__construct('user-form');
		$this->setAttribute('method', 'post');
		
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
				'type' => 'password',
				'name' => 'password',
				'options' => [
						'label' => 'USER_PASSWORD'
				]
		]);
		
		$this->add([
				'type' => 'checkbox',
				'name' => 'remember_me',
				'options' => [
						'label' => 'USER_REMEMBER_ME'
				]
		]);
		
		$this->add([
				'type' => 'hidden',
				'name' => 'redirect_url'
		]);
		
		$this->add([
				'type' => 'csrf',
				'name' => 'csrf',
				'options' => [
						'csrf_options' => [
								'timeout' => 600
						]
				]
		]);
		
		$this->add([
				'type' => 'submit',
				'name' => 'submit',
				'attributes' => [
						'value' => 'USER_SIGN_IN',
						'id' => 'submit'
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
								'name' => 'EmailAddress',
								'options' => [
										'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
										'useMxCheck' => false
								]
						]
				]
		]);
		
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
				'name' => 'remember_me',
				'required' => false,
				'filters' => [],
				'validators' => [
						[
								'name' => 'InArray',
								'options' => [
										'haystack' => [
												0,
												1
										]
								]
						]
				]
		]);
		
		$inputFilter->add([
				'name' => 'redirect_url',
				'required' => false,
				'filters' => [
						[
								'name' => 'StringTrim'
						]
				],
				'validators' => [
						[
								'name' => 'StringLength',
								'options' => [
										'min' => 0,
										'max' => 2048
								]
						]
				],
            ]);
    }        
}

