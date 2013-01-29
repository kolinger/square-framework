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

	const CACHE_NAMESPACE = 'Square.Model';
	const FLUSH = true,
		WITHOUT_FLUSH = false;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $entityManager;

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $repository;

	/**
	 * @var \Nette\Caching\Cache
	 */
	private $cache;



	/**
	 * @param \Doctrine\ORM\EntityRepository $repository
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param \Nette\Caching\IStorage $storage
	 */
	public function __construct(\Doctrine\ORM\EntityRepository $repository, \Doctrine\ORM\EntityManager $entityManager, \Nette\Caching\IStorage $storage)
	{
		$this->entityManager = $entityManager;
		$this->repository = $repository;
		$this->cache = new \Nette\Caching\Cache($storage, self::CACHE_NAMESPACE);
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
	 * @return \Nette\Caching\Cache
	 */
	public function getCache()
	{
		return $this->cache;
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
		try {
			$this->entityManager->persist($entity);
			if ($flush == self::FLUSH) {
				$this->entityManager->flush();
			}
		} catch(\Doctrine\DBAL\DBALException $exception) {
			Helper::convertException($exception);
		} catch (\PDOException $exception) {
			Helper::convertException($exception);
		}
	}



	/**
	 * @param object $entity
	 * @param boolean $flush
	 */
	public function remove($entity, $flush = self::FLUSH)
	{
		try {
			$this->entityManager->remove($entity);
			if ($flush == self::FLUSH) {
				$this->entityManager->flush();
			}
		} catch(\Doctrine\DBAL\DBALException $exception) {
			Helper::convertException($exception);
		} catch (\PDOException $exception) {
			Helper::convertException($exception);
		}
	}



	public function flush()
	{
		$this->entityManager->flush();
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