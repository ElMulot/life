<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace User\Service;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;
use User\Entity\User;

/**
 * Adapter used for authenticating user. It takes login and password on input
 * and checks the database if there is a user with such login (email) and password.
 * If such user exists, the service returns its identity (email). The identity
 * is saved to session and can be retrieved later with Identity view helper provided
 * by ZF3.
 * @package		User\Service
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class AuthAdapter implements AdapterInterface
{
	
	private $email;
	
	private $password;

	private $entityManager;

	public function __construct($entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = (string) $password;
	}

	/**
	 * Performs an authentication attempt
	 * @return Result
	 */
	public function authenticate()
	{
		// Check the database if there is a user with such email.
		$user = $this->entityManager->getRepository(User::class)->findOneByEmail($this->email);
		
		// If there is no such user, return 'Identity Not Found' status.
		if ($user == null)
		{
			return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null, [
					'USER_ACCESS_DENIED'
			]);
		}
		
		// If the user with such email exists, we need to check if it is active or retired.
		// Do not allow retired users to log in.
		if ($user->getStatus() == User::STATUS_RETIRED)
		{
			return new Result(Result::FAILURE, null, [
					'USER_RETIRED'
			]);
		}
		
		// Now we need to calculate hash based on user-entered password and compare
		// it with the password hash stored in database.
		$bcrypt = new Bcrypt();
		$passwordHash = $user->getPassword();
				
		if ($bcrypt->verify($this->password, $passwordHash))
		{
			// Great! The password hash matches. Return user identity (email) to be
			// saved in session for later use.
			return new Result(Result::SUCCESS, $user, [
					'USER_ACCESS_GRANTED'
			]);
		}
		
		// If password check didn't pass return 'Invalid Credential' failure status.
		return new Result(Result::FAILURE_CREDENTIAL_INVALID, 
                null,
                ['USER_ACCESS_DENIED']);        
    }
}


