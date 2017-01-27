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
use Blog\Entity\Tag;
use Zend\Filter\StaticFilter;
use User\Entity\Food;

/**
 * @package		Blog\Service
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class PostManager
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
	 * @param User $user
	 */
	public function add($data, $user)
	{
		$post = new Post();
		$post->setUser($user);
		$post->setLanguage($data['language']);
		$post->setTitle($data['title']);
		$post->setImage($data['image']);
		$post->setContent($data['content']);
		$post->setStatus($data['status']);
		$this->addFoodsToPost($data['foods'], $post);
		$this->entityManager->persist($post);
		$this->addTagsToPost($data['tags'], $post);
		$this->entityManager->flush();
	}

	/**
	 * @param array $data
	 * @param Post $post
	 */
	public function update($data, $post)
	{
		$post->setTitle($data['title']);
		if (!$data['has_already_image'] || $data['image'])
			$post->setImage($data['image']);
		$post->setContent($data['content']);
		$post->setStatus($data['status']);	
		$this->addFoodsToPost($data['foods'], $post);
		$this->addTagsToPost($data['tags'], $post);
		$this->entityManager->flush();
	}
	
	/**
	 * @param Post $post
	 */
	public function delete($post)
	{
		$tags = $post->getTags();
		foreach ($tags as $tag) {
			$post->removeTagAssociation($tag);
			if (count($tag->getPosts()) == 0) //use to prevent orphan tags
			{
				$this->entityManager->remove($tag);
			}
		}
		$this->entityManager->flush();
		$this->entityManager->remove($post);
		$this->entityManager->flush();
	}

	/**
	 * @param Food[] $foodsArray
	 * @param Post $post
	 */
	private function addFoodsToPost($foodsArray, $post)
	{
		// Remove food associations (if any)
		$foods = $post->getFoods();
		foreach ($foods as $food)
		{
			$post->removeFoodAssociation($food);
		}
		
		// Add foods to post
		if (isset($foodsArray)) {
			foreach ($foodsArray as $foodId)
			{
				if ($food = $this->entityManager->getRepository(Food::class)->findOneById($foodId))
						$post->addFood($food);
			}
		}
	}

	/**
	 * @param string $tagsStr Tags name separated by commas
	 * @param Post $post
	 */
	private function addTagsToPost($tagsStr, $post)
	{
		// Remove tag associations (if any)
		$tags = $post->getTags();
		foreach ($tags as $tag)
		{
			$post->removeTagAssociation($tag);
			if (count($tag->getPosts()) == 0) //use to prevent orphan tags
			{
				$this->entityManager->remove($tag);
			}
		}
		$this->entityManager->flush();
		
		// Add tags to post
		$tags = explode(',', $tagsStr);
		foreach ($tags as $tagName)
		{
				
			$tagName = StaticFilter::execute($tagName, 'StringTrim');
			if (empty($tagName))
			{
				continue;
			}
				
			$tag = $this->entityManager->getRepository(Tag::class)->findOneByName($tagName);
			if ($tag == null)
				$tag = new Tag();
					
				$tag->setName($tagName);
				$tag->addPost($post);
					
				$this->entityManager->persist($tag);
					
				$post->addTag($tag);
		}
	}
	
	/**
	 * @param Post $post
	 * @return string Post status
	 */
	public function getPostStatusAsString($post)
	{
		switch ($post->getStatus())
		{
			case Post::STATUS_DRAFT :
				return 'Brouillon';
			case Post::STATUS_PUBLISHED :
				return 'Publié';
		}
		
		return 'Unknown';
	}

	/**
	 * @param Post $post
	 * @param bool $addFoodLink If true, add also to the list food tags
	 * @return string Tags (and Food) separated by commas
	 */
	public function convertTagsToString($post, $addFoodLink = true)
	{
		$tagsStr = '';
		$foodCount = 0;
		if ($addFoodLink)
		{
			$foods = $post->getFoods();
			$foodCount = count($foods);
			$i = 0;
			foreach ($foods as $food)
			{
				$i++;
				$tagsStr .= $food->getFoodName();
				if ($i < $foodCount)
					$tagsStr .= ', ';
			}
		}		
		$tags = $post->getTags();
		$tagCount = count($tags);
		if ($foodCount != 0 && $tagCount !=0)
			$tagsStr .= ', ';
		$i = 0;
		foreach ($tags as $tag)
		{
			$i++;
			$tagsStr .= $tag->getName();
			if ($i < $tagCount)
				$tagsStr .= ', ';
		}
		
		return $tagsStr;
	}

	/**
	 * @param Post $post
	 * @return string Number of comments for the given post (eg. 1 Comment)
	 */
	public function getCommentCountStr($post)
	{
		$commentCount = count($post->getComments());
		if ($commentCount == 0)
			return $this->translator->translate('BLOG_NO_COMMENT');
		else if ($commentCount == 1)
			return $this->translator->translate('BLOG_1_COMMENT');
		else
			return $commentCount . ' ' . $this->translator->translate('BLOG_COMMENTS');
	}

	/**
	 * @return array Number of occurence for each tag (and food) associated to each post
	 */
	public function getTagCloud()
	{
		$tagCloud = [];
		
		$posts = $this->entityManager->getRepository(Post::class)->findPostsHavingAnyTag($this->translator->getLocale());
		$totalPostCount = count($posts);
		
		$foods = $this->entityManager->getRepository(Food::class)->findAll();
		foreach ($foods as $food)
		{
			$postsByFood = $this->entityManager->getRepository(Post::class)->findPostsByTag($food->getFoodName(), $this->translator->getLocale());
				
			$postCount = count($postsByFood);
			if ($postCount > 0)
			{
				$tagCloud[$food->getFoodName()] = $postCount;
			}
		}
		
		
		$tags = $this->entityManager->getRepository(Tag::class)->findAll();
		foreach ($tags as $tag)
		{
			$postsByTag = $this->entityManager->getRepository(Post::class)->findPostsByTag($tag->getName(), $this->translator->getLocale());
			
			$postCount = count($postsByTag);
			if ($postCount > 0)
			{
				$tagCloud[$tag->getName()] = $postCount;
			}
		}
		
		$normalizedTagCloud = [];
		
		// Normalize
		foreach ($tagCloud as $name => $postCount)
		{
			$normalizedTagCloud[$name] = $postCount / $totalPostCount;
		}
		
		return $normalizedTagCloud;
	}
}