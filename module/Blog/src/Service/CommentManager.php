<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Blog\Service;

use Blog\Entity\Post;
use Blog\Entity\Comment;

/**
 * @package		Blog\Service
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class CommentManager
{
	private $entityManager;
	
	public function __construct($entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param array $data
	 * @param Post $post
	 * @param User $user
	 */
	public function add($data, $post, $user)
	{
		$comment = new Comment();
		$comment->setPost($post);
		if ($user)
		{
			$comment->setUser($user);
		} else {
			$comment->setGuestName($data['author']);
		}
		$comment->setContent($data['comment']);
		
		$this->entityManager->persist($comment);
		$this->entityManager->flush();
	}
	
	/**
	 * @param array $data
	 * @param Comment $comment
	 */
	public function update($data, $comment)
	{
		$comment->setContent($data['comment']);
		$this->entityManager->flush();
	}
	
	/**
	 * @param Comment $comment
	 */
	public function delete($comment)
	{
		$this->entityManager->remove($comment);
		$this->entityManager->flush();
	}
}