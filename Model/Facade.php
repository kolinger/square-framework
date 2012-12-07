<?php

namespace Square\Model;

/**
 * @author Tomáš Kolinger
 *
 * @property-read \Doctrine\ORM\EntityManager $entityManager
 * @property-read \Doctrine\ORM\EntityRepository $repository
 */
abstract class Facade extends \Nette\Object
{
	const FLUSH = true;
	const WITHOUT_FLUSH = false;

	/** @var \Doctrine\ORM\EntityManager */
	private $entityManager;

	/** @var \Doctrine\ORM\EntityRepository */
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
	 * @param object
	 * @param boolean
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
	 * @param object
	 * @param boolean
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
	 * @param string
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	protected function createQueryBuilder($alias)
	{
		return $this->repository->createQueryBuilder($alias);
	}
}