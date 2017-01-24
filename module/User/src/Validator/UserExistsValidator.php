<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace User\Validator;

use Zend\Validator\AbstractValidator;
use User\Entity\User;

/**
 * @package		User\Validator
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class UserExistsValidator extends AbstractValidator
{
	/**
	 * Available validator options.
	 */
	protected $options = array(
			'entityManager' => null,
			'user' => null
	);
	
	// Validation failure message IDs.
	const NOT_SCALAR = 'notScalar';
	const USER_EXISTS = 'userExists';
	
	/**
	 * Validation failure messages.
	 */
	protected $messageTemplates = array(
			self::NOT_SCALAR => "The email must be a scalar value",
			self::USER_EXISTS => "Another user with such an email already exists"
	);

	public function __construct($options = null)
	{
		if (is_array($options))
		{
			if (isset($options['entityManager']))
				$this->options['entityManager'] = $options['entityManager'];
			if (isset($options['user']))
				$this->options['user'] = $options['user'];
		}
		
		parent::__construct($options);
	}

	/**
	 * Check if user exists
	 * @param int $value
	 * @return bool
	 */
	public function isValid($value)
	{
		if (!is_scalar($value))
		{
			$this->error(self::NOT_SCALAR);
			return $false;
		}
		
		$entityManager = $this->options['entityManager'];
		
		$user = $entityManager->getRepository(User::class)->findOneByEmail($value);
		
		if ($this->options['user'] == null)
		{
			$isValid = ($user == null);
		} else
		{
			if ($this->options['user']->getEmail() != $value && $user != null)
				$isValid = false;
			else
				$isValid = true;
		}
		
		if (!$isValid)
		{
			$this->error(self::USER_EXISTS);
		}
		
		return $isValid;
    }
}

