<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace User\Repository;

use Doctrine\ORM\EntityRepository;
use User\Entity\Waste;
use User\Entity\Food;

/**
 * @package		User\Repository
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class SavedFoodRepository extends EntityRepository
{
	/**
	 * @return User[]
	 */
	public function findUsersWithWastedFood()
	{
		$entityManager = $this->getEntityManager();
	
		$queryBuilder = $entityManager->createQueryBuilder();
	
		$queryBuilder->select('p')
		->from(Post::class, 'p')
		->join('p.tags', 't')
		->where('p.status = ?1')
		->orderBy('p.dateCreated', 'DESC')
		->setParameter('1', Post::STATUS_PUBLISHED);
	
		$posts = $queryBuilder->getQuery()->getResult();
	
		return $posts;
	}
}