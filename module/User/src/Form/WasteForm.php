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
use User\Entity\Food;

/**
 * @package		User\Form
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class WasteForm extends Form
{
	
	private $entityManager = null;

	private $user;

	public function __construct($entityManager, $user)
	{
		parent::__construct('waste-form');
		$this->setAttribute('method', 'post');
		$this->entityManager = $entityManager;
		$this->user = $user;
		$this->addElements();
		$this->addInputFilter();
	}

	protected function addElements()
	{

		$foods = $this->entityManager->getRepository(Food::class)->findBy(['language' => $this->user->getLanguage()], ['foodName' => 'ASC']);
		foreach ($foods as $food) {
			$array[$food->getId()] = $food->getFoodName();
		}
		$this->add([
				'type' => 'select',
				'name' => 'food',
				'options' => [
						'label' => 'WASTE_FOOD',
						'value_options' => $array,
				],
				'attributes' => [
						'value' => 0,
						'data-live-search' => true
				]
		]);

		$this->add([
				'type'  => 'text',
				'name' => 'quantity',
				'options' => [
						'label' => 'WASTE_QUANTITY',
				],
		]);
		
		$this->add([
				'type' => 'select',
				'name' => 'UOM',
				'options' => [
						'label' => 'WASTE_UOM',
						'value_options' => [
								'Unit' => 'UNIT',
								'kg' => 'KG',
								'L' => 'L'
						]
				],
				'attributes' => [
						'value' => 0
				]
		]);

		$this->add([
				'type'  => 'text',
				'name' => 'why',
				'options' => [
						'label' => 'WASTE_WHY',
				],
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
				'name'     => 'food',
				'required' => true,
				'filters'  => [
						['name' => 'StringTrim'],
						['name' => 'StripTags'],
						['name' => 'StripNewlines'],
				],
				'validators' => [
						[
								'name'    => 'StringLength',
								'options' => [
										'min' => 1,
										'max' => 100
								],
						],
				],
		]);

		$inputFilter->add([
				'name'     => 'quantity',
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
				'name' => 'UOM',
				'required' => true,
				'filters' => [
						['name' => 'StringTrim'],
						['name' => 'StripTags'],
						['name' => 'StripNewlines'],
				],
				'validators' => [
						[
								'name' => 'InArray',
								'options' => [
										'haystack' => [
												'Unit',
												'kg',
												'L'
										]
								]
						]
				]
		]);
		
		$inputFilter->add([
				'name'     => 'why',
				'required' => true,
				'filters'  => [
						['name' => 'StringTrim'],
						['name' => 'StripTags'],
						['name' => 'StripNewlines'],
				],
				'validators' => [
						[
								'name'    => 'StringLength',
								'options' => [
										'min' => 1,
										'max' => 1000
								],
						],
				],
		]);
	}
}
