<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Blog\Entity\Post;
use User\Entity\User;
use Blog\Form\PostForm;
use Blog\Form\CommentForm;
use Blog\Entity\Comment;
use User\Entity\Food;

/**
 * @package		Blog\Controller
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

class PostController extends AbstractActionController
{
	public $entityManager;

	private $postManager;

	private $imageManager;

	private $commentManager;

	private $sessionContainer;

	private $translator;

	private $config;

	public function __construct($entityManager, $postManager, $imageManager, $commentManager, $sessionContainer, $translator, $config)
	{
		$this->entityManager = $entityManager;
		$this->postManager = $postManager;
		$this->imageManager = $imageManager;
		$this->commentManager = $commentManager;
		$this->sessionContainer = $sessionContainer;
		$this->translator = $translator;
		$this->config = $config;
	}

	public function viewAction()
	{
		$postId = (int) $this->params()->fromRoute('id', -1);
		$commentAction = (string) $this->params()->fromRoute('comment_action', '');
		$commentId = (int) $this->params()->fromRoute('comment_id', -1);
		
		if ($postId < 0)
		{
			$this->flashMessenger()->addErrorMessage('BLOG_MESSAGE_COMMENT_ERROR');
			return $this->redirect()->toRoute('blog');
		}
		
		$post = $this->entityManager->getRepository(Post::class)->findOneById($postId);
		if ($post == null)
		{
			$this->flashMessenger()->addErrorMessage('BLOG_MESSAGE_COMMENT_ERROR');
			return $this->redirect()->toRoute('blog');
		}
		
		if ($commentAction) {
			if (($commentAction != 'edit_comment' && $commentAction != 'delete_comment') || $commentId < 0)
			{
				$this->flashMessenger()->addErrorMessage('BLOG_MESSAGE_COMMENT_ERROR');
				return $this->redirect()->toRoute('blog');
			}
			
			$comment = $this->entityManager->getRepository(Comment::class)->findOneById($commentId);
			if ($comment == null)
			{
				$this->flashMessenger()->addErrorMessage('BLOG_MESSAGE_COMMENT_ERROR');
				return $this->redirect()->toRoute('blog');
			}
		}

		if ($this->Identity())
			$user = $this->entityManager->getRepository(User::class)->findOneById($this->Identity()->getId());
		else
			$user = null;
		
		if ($commentAction == 'delete_comment')
		{
			$this->commentManager->delete($comment);
			$this->flashMessenger()->addSuccessMessage('BLOG_MESSAGE_COMMENT_DELETE');
			return $this->redirect()->toRoute('posts', [
					'action' => 'view',
					'id' => $postId,
					'postManager' => $this->postManager,
			]);
		}
		elseif ($commentAction == 'edit_comment')
			$form = new CommentForm($user, $comment);
		else 
			$form = new CommentForm($user);
		
		if ($this->getRequest()->isPost())
		{
			$data = $this->params()->fromPost();
			$form->setData($data);

			if ($form->isValid())
			{
				$data = $form->getData();
				
				if ($commentAction == 'edit_comment') {
					$this->commentManager->update($data, $comment);
					$this->flashMessenger()->addSuccessMessage('BLOG_MESSAGE_COMMENT_UPDATE');
					return $this->redirect()->toRoute('posts', [
							'action' => 'view',
							'id' => $postId,
							'postManager' => $this->postManager,
					]);
				}
				else
				{
					$this->commentManager->add($data, $post, $user);
					$this->flashMessenger()->addSuccessMessage('BLOG_MESSAGE_COMMENT_ADD');
					return $this->redirect()->toRoute('posts', [
							'action' => 'view',
							'id' => $postId,
							'postManager' => $this->postManager,
					]);
				}
			}
		}
		
		if ($commentAction == 'edit_comment') {
			return new ViewModel([
					'post' => $post,
					'form' => $form,
					'commentId' => $commentId,
					'postManager' => $this->postManager,
			]);
		} else {
			return new ViewModel([
					'post' => $post,
					'form' => $form,
					'postManager' => $this->postManager,
			]);
		}
	}

	public function adminAction()
	{
		$this->sessionContainer->step = 1;
		$posts = $this->entityManager->getRepository(Post::class)->findBy([], [
				'status' => 'ASC',
				'dateCreated' => 'DESC'
		]);
		
		return new ViewModel([
				'posts' => $posts,
				'postManager' => $this->postManager
		]);
	}

	public function addAction()
	{
		$step = 1;
		if (isset($this->sessionContainer->step))
		{
			$step = $this->sessionContainer->step;
		}
		
		if ($step < 1 || $step > 2)
			$step = 1;
		
		if ($step == 1)
		{
			$form = new PostForm($this->entityManager, $this->translator, $this->config, $step);
			
			if ($this->getRequest()->isPost())
			{
				$data = $this->params()->fromPost();
				$form->setData($data);
				if ($form->isValid())
				{
					$data = $form->getData();
					$step++;
					$this->sessionContainer->step = $step;
					$this->sessionContainer->language = $data['language'];
					return $this->redirect()->toRoute('posts', [
							'action' => 'add'
					]);
				}
			}
		}
		else
		{			
			$language = $this->sessionContainer->language;
			if (!isset($this->sessionContainer->language))
			{
				$step = 1;
				return $this->redirect()->toRoute('posts', [
						'action' => 'add'
				]);
			}
			$form = new PostForm($this->entityManager, $this->translator, $this->config, $step, $language);
			
			if ($this->getRequest()->isPost())
			{				
				$data = $this->params()->fromPost();
				$form->setData($data);				
				if ($form->isValid())
				{
					$data = $form->getData();
					
					$data['content'] = $this->imageManager->storeImages($data['content']);
					$data['language'] = $this->sessionContainer->language;
					$user = $this->entityManager->getRepository(User::class)->findOneById($this->Identity()->getId());
					$this->postManager->add($data, $user);
					return $this->redirect()->toRoute('posts');
				}
			}
		}
		
		
		return new ViewModel([
				'form' => $form,
				'step' => $step
		]);
	}

	public function editAction()
	{
		$postId = (int) $this->params()->fromRoute('id', -1);
		
		if ($postId < 0)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}
		
		$post = $this->entityManager->getRepository(Post::class)->findOneById($postId);
		if ($post == null)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}
		
		if ($this->getRequest()->isPost())
		{
			$data = $this->params()->fromPost();
			$form = new PostForm($this->entityManager, $this->translator, $this->config, $data['language']);
			$form->setData($data);
			if ($form->isValid())
			{
				$data = $form->getData();
				$data['content'] = $this->imageManager->storeImages($data['content']);
				$this->postManager->update($data, $post);
				return $this->redirect()->toRoute('posts', [
						'action' => 'admin'
				]);
			}
		}
		else
		{
			$form = new PostForm($this->entityManager, $this->translator, $this->config, $post->getLanguage());
			$data = [
					'language' => $post->getLanguage(),
					'title' => $post->getTitle(),
					'cancel_image' => $post->getImage(),
					'have_already_image' => true,
					'content' => $post->getContent(),
					'tags' => $this->postManager->convertTagsToString($post, false),
					'status' => $post->getStatus()
			];
			
			foreach ($post->getFoods() as $food)
			{
				$data['foods'][] = $food->getId();
			}
			
			$form->setData($data);
		}
		
		return new ViewModel([
				'form' => $form,
				'post' => $post
		]);
	}

	public function deleteAction()
	{
		$postId = (int) $this->params()->fromRoute('id', -1);
		
		// Validate input parameter
		if ($postId < 0)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}
		
		$post = $this->entityManager->getRepository(Post::class)->findOneById($postId);
		if ($post == null)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}
		$this->postManager->delete($post);
		
		// Redirect the user to "admin" page.
		return $this->redirect()->toRoute('posts', [
				'action' => 'admin'
		]);
	}

	public function fileAction()
	{
		// Get the file name from GET variable
		$fileName = $this->params()->fromQuery('name', '');
		
		// Validate input parameters
		if (strlen($fileName) > 128)
		{
			throw new \Exception('File name is empty or too long');
		} elseif (empty($fileName)) {
			$this->getResponse()->setStatusCode(404);
			return;
		} else {
			$fileName = $this->imageManager->getImagePath($fileName);
		}
		
		// Get image file info (size and MIME type).
		$fileInfo = $this->imageManager->getImageFileInfo($fileName);
		if ($fileInfo === false)
		{
			$this->getResponse()->setStatusCode(404);
			return;
		}
		
		// Write HTTP headers.
		$response = $this->getResponse();
		$headers = $response->getHeaders();
		$headers->addHeaderLine("Content-type: " . $fileInfo['type']);
		$headers->addHeaderLine("Content-length: " . $fileInfo['size']);
		
		// Write file content
		$fileContent = $this->imageManager->getImageFileContent($fileName);
		if ($fileContent !== false)
		{
			$response->setContent($fileContent);
		}
		else
		{
			// Set 500 Server Error status code
			$this->getResponse()->setStatusCode(500);
			return;
		}
		
		// Return Response to avoid default view rendering.
		return $this->getResponse();
	}
}
