<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Blog\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * @package		Blog\Form
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

class CommentForm extends Form
{
	private $user = null;

	private $comment = null;

	public function __construct($user = null, $comment = null)
	{
		parent::__construct('comment-form');
		$this->setAttribute('method', 'post');
		$this->user = $user;
		$this->comment = $comment;
		
		$this->addElements();
		$this->addInputFilter();
	}

	protected function addElements()
	{
		if ($this->user == null)
		{
			$this->add([
					'type' => 'text',
					'name' => 'author',
					'options' => [
							'label' => 'BLOG_NAME'
					]
			]);
		}
		
		if ($this->comment)
		{
			$this->add([
					'type' => 'textarea',
					'name' => 'comment',
					'options' => [
							'label' => 'BLOG_COMMENT'
					],
					'attributes' => [
							'value' => $this->comment->getContent()
					]
			]);
		}
		else
		{
			$this->add([
					'type' => 'textarea',
					'name' => 'comment',
					'options' => [
							'label' => 'BLOG_COMMENT'
					]
			]);
		}
		
		if ($this->comment)
		{
			$this->add([
					'type' => 'submit',
					'name' => 'submit',
					'attributes' => [
							'value' => 'UPDATE'
					]
			]);
		}
		elseif ($this->user)
		{
			$this->add([
					'type' => 'submit',
					'name' => 'submit',
					'attributes' => [
							'value' => 'BLOG_POST'
					]
			]);
		}
		else
		{
			$this->add([
					'type' => 'submit',
					'name' => 'submit',
					'attributes' => [
							'value' => 'BLOG_POST_AS_GUEST'
					]
			]);
		}
		
		$this->add([
				'type'  => 'button',
				'name' => 'cancel',
				'options' => [
						'label' => 'CANCEL'
				],
		]);
	}

	private function addInputFilter()
	{
		$inputFilter = new InputFilter();
		$this->setInputFilter($inputFilter);
		
		if ($this->user == null)
		{
			$inputFilter->add([
					'name' => 'author',
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
							]
					]
			]);
		}
		
		$inputFilter->add([
				'name' => 'comment',
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
    }
}

