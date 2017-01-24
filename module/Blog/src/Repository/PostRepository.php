<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Blog\Repository;

use Doctrine\ORM\EntityRepository;
use Blog\Entity\Post;
use Blog\Entity\Tag;

/**
 * @package		Blog\Repository
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

class PostRepository extends EntityRepository
{
	
	/**
	 * Finds the 5 first posts by language
	 * @param string $language Language of the post (eg. fr_FR)
	 * @return array
	 */
	public function findFirstPosts($language)
	{
		$entityManager = $this->getEntityManager();
	
		$queryBuilder = $entityManager->createQueryBuilder();
	
		$queryBuilder->select('p')
		->from(Post::class, 'p')
		->where('p.status = ?1')
		->andwhere('p.language = ?2')
		->orderBy('p.dateCreated', 'DESC')
		->setMaxResults(5)
		->setParameter('1', Post::STATUS_PUBLISHED)
		->setParameter('2', $language);
	
		$posts = $queryBuilder->getQuery()->getResult();
		return $posts;
	}
	
    /**
     * Finds all published posts having any tag
	 * @param string $language Language of the post (eg. fr_FR)
     * @return array
     */
    public function findPostsHavingAnyTag($language)
    {
    	$entityManager = $this->getEntityManager();
        
        $queryBuilder = $entityManager->createQueryBuilder();
        
        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->leftJoin('p.foods', 'f')
            ->leftJoin('p.tags', 't')
            ->where('p.status = ?1')
            ->andwhere('p.language = ?2')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_PUBLISHED)
            ->setParameter('2', $language);
        
        $posts = $queryBuilder->getQuery()->getResult();
        return $posts;
    }
    
    /**
     * Finds all published posts having the given tag or food
     * @param string $tagName Name of the tag or the food
     * @param string $language Language of the post (eg. fr_FR)
     * @return array
     */
    public function findPostsByTag($tagName, $language)
    {
        $entityManager = $this->getEntityManager();
        
        $queryBuilder = $entityManager->createQueryBuilder();
        
        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->leftJoin('p.tags', 't')
            ->leftJoin('p.foods', 'f')
            ->where($queryBuilder->expr()->orX('t.name = ?2', 'f.foodName = ?2'))
            ->andWhere('p.status = ?1')
            ->andWhere('p.language = ?3')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_PUBLISHED)
            ->setParameter('2', $tagName)
            ->setParameter('3', $language);
        
		$posts = $queryBuilder->getQuery()->getResult();
        
        return $posts;
    }
}