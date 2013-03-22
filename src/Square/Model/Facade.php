<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Model;


use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Object;

/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 *
 * @property-read EntityManager $entityManager
 * @property-read EntityRepository $repository
 */
abstract class Facade extends Object
{

	const CACHE_NAMESPACE = 'Square.Model';
	const FLUSH = TRUE,
		WITHOUT_FLUSH = FALSE;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var EntityRepository
	 */
	private $repository;

	/**
	 * @var Cache
	 */
	private $cache;



	/**
	 * @param EntityRepository $repository
	 * @param EntityManager $entityManager
	 * @param IStorage $storage
	 */
	public function __construct(EntityRepository $repository, EntityManager $entityManager, IStorage $storage)
	{
		$this->entityManager = $entityManager;
		$this->repository = $repository;
		$this->cache = new Cache($storage, self::CACHE_NAMESPACE);
	}



	/**
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}



	/**
	 * @return EntityRepository
	 */
	public function getRepository()
	{
		return $this->repository;
	}



	/**
	 * @return Cache
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
		} catch(DBALException $exception) {
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
		} catch(DBALException $exception) {
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
	 * @return QueryBuilder
	 */
	protected function createQueryBuilder($alias)
	{
		return $this->repository->createQueryBuilder($alias);
	}



	/**
	 * @param array $mapping
	 * @param array $filter
	 * @param array $order
	 * @return array
	 */
	public function getDatagridData(array $mapping, $filter = NULL, $order = NULL)
	{
		$name = 'entity';
		$qb = $this->createQueryBuilder($name);

		$this->applyDatagridFilters($qb, $name, $mapping, $filter);
		$this->applyDatagridOrder($qb, $name, $mapping, $order);

		$q = $qb->getQuery();
		return $q->getResult();
	}



	/**
	 * @param QueryBuilder $qb
	 * @param string $name
	 * @param array $mapping
	 * @param array $filter
	 */
	protected function applyDatagridFilters(QueryBuilder $qb, $name, array $mapping, $filter = NULL)
	{
		if ($filter) {
			$number = 1;
			foreach ($filter as $column => $value) {
				if (isset($mapping[$column])) {
					$column = $mapping[$column]['accessor'];
				} else {
					$column = $name . '.' . $column;
				}
				if ($number == 1) {
					$qb->where($qb->expr()->like($column, '?' . $number))
						->setParameter($number, '%' . $value . '%');
				} else {
					$qb->where($qb->expr()->like($column, '?' . $number))
						->setParameter($number, '%' . $value . '%');
				}
				$number++;
			}
		}
	}



	/**
	 * @param QueryBuilder $qb
	 * @param string $name
	 * @param array $mapping
	 * @param array $order
	 */
	protected function applyDatagridOrder(QueryBuilder $qb, $name, array $mapping, $order = NULL)
	{
		if ($order) {
			if (isset($mapping[$order[0]])) {
				$column = $mapping[$order[0]]['accessor'];
			} else {
				$column = $name . '.' . $order[0];
			}
			$qb->orderBy($column, $order[1]);
		}
	}

}
