<?php

namespace TinyCms\NodeProvider\Storage\PDO\Classes;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Storage\Library\EntityManagerInterface;

class EntityManager implements EntityManagerInterface {

	/*
	 * @var PDO
	 */
	protected $conn;

	/*
	 * @var array of TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface
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
	 * @param $conn \PDO
	 */
	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->repositories = array();
		$this->entitiesToInsert = array();
	}

	/*
	 * @return \PDO
	 */
	public function getConnection()
	{
		return $this->conn;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function update(EntityInterface $entity)
	{
		$this->entitiesToUpdate[] = $entity;
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
					$repository = new $repositoryClass($this->conn, $this, $type);
				}
				else
				{
					$repository = new EntityRepository($this->conn, $this, $type);
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
		
		// handle all entity updates
		if (!empty($this->entitiesToUpdate))
		{
			foreach ($this->entitiesToUpdate as $entity)
			{
				$entity->_resetUpdate();
			}
			$this->entitiesToUpdate = array();
		}
	}
}