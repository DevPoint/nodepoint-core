<?php

namespace TinyCms\NodeProvider\Storage\PDO;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Library\EntityManagerInterface;

class EntityManager implements EntityManagerInterface {

	/*
	 * @var PDO
	 */
	protected $conn;

	/*
	 * @var array of TinyCms\NodeProvider\Library\EntityRepositoryInterface
	 */
	protected $repositories;

	/*
	 * @var array of TinyCms\NodeProvider\Library\EntityInterface
	 */
	protected $entitiesToInsert;

	/*
	 * @var array of TinyCms\NodeProvider\Library\EntityInterface
	 */
	protected $entitiesToUpdate;

	/*
	 * @param $type TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->repositories = array();
		$this->entitiesToInsert = array();
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function persist(EntityInterface $entity)
	{
		$repository = $entity->_getRepository();
		if (!$repository)
		{
			$type = $entity->_type();
			$typeName = $type->getTypeName();
			if (!isset($this->repositories[$typeName]))
			{
				$repositoryClass = $type->getRepositoryClass();
				if ($repositoryClass)
				{
					$repository = new $repositoryClass($this->conn, $type);
				}
				else
				{
					$repository = new EntityRepository($this->conn, $type);
				}
				$this->repositories[$typeName] = $repository;
			}
			$repository = $this->repositories[$typeName];
			$entity->_setRepository($repository);
			$this->entitiesToInsert[] = $entity;
		}
	}

	/*
	 * Writes all changes back to storage
	 */
	public function flush()
	{
		$this->entitiesToInsert = array();
		$this->entitiesToUpdate = array();
	}
}