<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Model;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 *
 * @property-read \Doctrine\ORM\EntityManager $entityManager
 * @property-read \Doctrine\ORM\EntityRepository $repository
 */
abstract class Facade extends \Nette\Object
{

	const FLUSH = true;
	const WITHOUT_FLUSH = false;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $entityManager;

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $repository;



	/**
	 * @param \Doctrine\ORM\EntityRepository
	 * @param \Doctrine\ORM\EntityManager
	 */
	public function __construct(\Doctrine\ORM\EntityRepository $repository, \Doctrine\ORM\EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->repository = $repository;
	}



	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}



	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	public function getRepository()
	{
		return $this->repository;
	}



	/**
	 * @return object
	 */
	public function create()
	{
		$class = $this->repository->getClassName();
		$entity = new $class;
		return $entity;
	}



	/**
	 * @param object $entity
	 * @param boolean $flush
	 */
	public function save($entity, $flush = self::FLUSH)
	{
//		try {
			$this->entityManager->persist($entity);
			if ($flush == self::FLUSH) {
				$this->entityManager->flush();
			}
//		} catch () {
//
//		}
	}



	/**
	 * @param object $entity
	 * @param boolean $flush
	 */
	public function remove($entity, $flush = self::FLUSH)
	{
//		try {
			$this->entityManager->remove($entity);
			if ($flush == self::FLUSH) {
				$this->entityManager->flush();
			}
//		} catch () {
//
//		}
	}



	/**
	 * @return array
	 */
	public function findAll()
	{
		return $this->repository->findAll();
	}



	/**
	 * @param string $alias
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	protected function createQueryBuilder($alias)
	{
		return $this->repository->createQueryBuilder($alias);
	}

}