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
class LocaleForm extends Form
{

	private $sessionContainer;

	private $config;
	
	public function __construct($sessionContainer, $config)
	{
		parent::__construct('user-form');
		$this->setAttribute('method', 'post');
		$this->sessionContainer = $sessionContainer;
		$this->config = $config;
		
		$this->addElements();
		$this->addInputFilter();
	}

	protected function addElements()
	{
		$this->add([
				'type' => 'select',
				'name' => 'language',
				'options' => [
						'label' => 'LANGUAGE',
						'value_options' => $this->config['application_settings']['aviable_languages']
				],
				'attributes' => [
						'value' => (isset($this->sessionContainer['locale']))?$this->sessionContainer['locale']:$this->config['application_settings']['default_language']
				]
		]);
	}

	private function addInputFilter()
	{
		$inputFilter = new InputFilter();
		$this->setInputFilter($inputFilter);
		
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

