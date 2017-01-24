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

use User\Entity\User;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;

/**
 * @package		User\Service
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class UserManager
{

	private $entityManager;

	private $translator;
	
	public function __construct($entityManager, $translator)
	{
		$this->entityManager = $entityManager;
		$this->translator = $translator;
	}

	/**
	 * @param array $data
	 * @return User
	 */
	public function add($data)
	{
		// Do not allow several users with the same email address.
		if ($this->checkUserExists($data['email']))
		{
			throw new \Exception('USER_ALREADY_EXIST');
		}
		
		// Create new User entity.
		$user = new User();
		$user->setEmail($data['email']);
		$user->setFullName($data['full_name']);
		
		// Encrypt password and store the password in encrypted state.
		$bcrypt = new Bcrypt();
		$passwordHash = $bcrypt->create($data['password']);
		$user->setPassword($passwordHash);
		
		$user->setLanguage($data['language']);
		$this->translator->setLocale($data['language']);
		$user->setStatus(User::STATUS_ACTIVE);
		
		// Add the entity to the entity manager.
		$this->entityManager->persist($user);
		
		// Apply changes to database.
		$this->entityManager->flush();
		
		return $user;
	}
	
	/**
	 * @param array $data
	 * @param User $user
	 * @param string $scenario
	 * @return bool
	 */
	public function update($data, $user, $scenario)
	{
		// Do not allow to change user email if another user with such email already exits.
		if ($user->getEmail() != $data['email'] && $this->checkUserExists($data['email']))
		{
			throw new \Exception('USER_ALREADY_EXIST');
		}
		
		$user->setEmail($data['email']);
		$user->setFullName($data['full_name']);
		$user->setLanguage($data['language']);
		
		if ($scenario == 'admin')
			$user->setStatus($data['status']);
		else {
			$this->translator->setLocale($data['language']);
		}
		// Apply changes to database.
		$this->entityManager->flush();
		
		return true;
	}

	/**
	 * This method checks if at least one user presents, and if not, creates
	 * 'Admin' user with email 'root@life.com' and password 'root'
	 */
	public function createAdminUserIfNotExists()
	{
		$user = $this->entityManager->getRepository(User::class)->findOneBy([]);
		if ($user == null)
		{
			$user = new User();
			$user->setEmail('root@life.com');
			$user->setFullName('Admin');
			$bcrypt = new Bcrypt();
			$passwordHash = $bcrypt->create('root');
			$user->setPassword($passwordHash);
			$user->setStatus(User::STATUS_ADMIN);
			$user->setDateCreated(date('Y-m-d H:i:s'));
			
			$this->entityManager->persist($user);
			$this->entityManager->flush();
		}
	}

	/**
	 * Checks whether an active user with given email address already exists in the database
	 * @param string $email
	 * @return User|null
	 */
	public function checkUserExists($email)
	{
		$user = $this->entityManager->getRepository(User::class)->findOneByEmail($email);
		
		return $user !== null;
	}

	/**
	 * Checks that the given password is correct
	 * @param User $user
	 * @param string $password
	 * @return bool
	 */
	public function validatePassword($user, $password)
	{
		$bcrypt = new Bcrypt();
		$passwordHash = $user->getPassword();
		
		if ($bcrypt->verify($password, $passwordHash))
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Generates a password reset token for the user. This token is then stored in database and 
	 * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is 
	 * directed to the Set Password page
	 * @param User $user
	 */
	public function generatePasswordResetToken($user)
	{
		// Generate a token.
		$token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz', true);
		$user->setPasswordResetToken($token);
		
		$currentDate = date('Y-m-d H:i:s');
		$user->setPasswordResetTokenCreationDate($currentDate);
		
		$this->entityManager->flush();
		
		$subject = 'Password Reset';
		
		$httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
		$passwordResetUrl = 'http://' . $httpHost . '/set-password/' . $token;
		
		$body = 'USER_RESET_PASSWORD_MAIL_1\n';
		$body .= "$passwordResetUrl\n";
		$body .= "USER_RESET_PASSWORD_MAIL_2\n";
		
		// Send email to user.
		mail($user->getEmail(), $subject, $body);
	}

	/**
	 * Checks whether the given password reset token is a valid one
	 * @param string $passwordResetToken
	 * @return bool
	 */
	public function validatePasswordResetToken($passwordResetToken)
	{
		$user = $this->entityManager->getRepository(User::class)->findOneByPasswordResetToken($passwordResetToken);
		
		if ($user == null)
		{
			return false;
		}
		
		$tokenCreationDate = $user->getPasswordResetTokenCreationDate();
		$tokenCreationDate = strtotime($tokenCreationDate);
		
		$currentDate = strtotime('now');
		
		if ($currentDate - $tokenCreationDate > 24 * 60 * 60)
		{
			return false; // expired
		}
		
		return true;
	}

	/**
	 * This method sets new password by password reset token.
	 * @param string $passwordResetToken
	 * @param string $newPassword
	 * @return bool
	 */
	public function setNewPasswordByToken($passwordResetToken, $newPassword)
	{
		if (!$this->validatePasswordResetToken($passwordResetToken))
		{
			return false;
		}
		
		$user = $this->entityManager->getRepository(User::class)->findOneBy([
				'passwordResetToken' => $passwordResetToken
		]);
		
		if ($user === null)
		{
			return false;
		}
		
		// Set new password for user
		$bcrypt = new Bcrypt();
		$passwordHash = $bcrypt->create($newPassword);
		$user->setPassword($passwordHash);
		
		// Remove password reset token
		$user->setPasswordResetToken(null);
		$user->setPasswordResetTokenCreationDate(null);
		
		$this->entityManager->flush();
		
		return true;
	}

	/**
	 * This method is used to change the password for the given user. To change the password,
	 * one must know the old password.
	 * @param User $user
	 * @param array $data
	 * @return bool
	 */
	public function changePassword($user, $data)
	{
		$oldPassword = $data['old_password'];
		
		// Check that old password is correct
		if (!$this->validatePassword($user, $oldPassword))
		{
			return false;
		}
		
		$newPassword = $data['new_password'];
		
		// Check password length
		if (strlen($newPassword) < 4 || strlen($newPassword) > 64)
		{
			return false;
		}
		
		// Set new password for user
		$bcrypt = new Bcrypt();
		$passwordHash = $bcrypt->create($newPassword);
		$user->setPassword($passwordHash);
		
		// Apply changes
		$this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     */
    public function delete($user)
    {
    	$this->entityManager->remove($user);
    	$this->entityManager->flush();
    }
}

