<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Admin\Form;

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
class FoodForm extends Form
{
	
	private $entityManager = null;

	private $translator;
	
	private $config;
	
	public function __construct($entityManager, $translator, $config)
	{
		parent::__construct('food-form');
		$this->setAttribute('method', 'post');
		$this->entityManager = $entityManager;
		$this->translator = $translator;
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
						'value' => $this->translator->getLocale()
				]
		]);

		$this->add([
				'type' => 'text',
				'name' => 'food_name',
				'options' => [
						'label' => "Nom de l'aliment"
				]
		]);
		
		$this->add([
				'type' => 'text',
				'name' => 'weight',
				'options' => [
						'label' => "Poids approximatif (en g)"
				]
		]);

		$this->add([
				'type' => 'textarea',
				'name' => 'comment',
				'options' => [
						'label' => "Commentaire"
				]
		]);

		$this->add([
				'type'  => 'submit',
				'name' => 'submit',
				'attributes' => [
						'value' => 'SAVE',
						'id' => 'submitbutton',
				],
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
		
		
		$inputFilter->add([
				'name' => 'food_name',
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
		
		$inputFilter->add([
				'name'     => 'weight',
				'required' => true,
				'filters'  => [
						['name' => 'StringTrim'],
						['name' => 'StripTags'],
						['name' => 'StripNewlines'],
				],
				'validators' => [
						[
								'name'    => 'Digits',
						],
				],
		]);
		
		$inputFilter->add([
				'name'     => 'comment',
				'required' => true,
				'validators' => [
						[
								'name'    => 'StringLength',
								'options' => [
										'min' => 0,
										'max' => 4096
								],
						],
				],
		]);
	}
}
