<?php

namespace TinyCms\NodeProvider\Storage\PDO\Library;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Storage\Library\EntityManagerInterface;
use TinyCms\NodeProvider\Storage\PDO\Library\EntityStorageProxy;

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
	protected $entitiesToUpdate;

	/*
	 * @param $conn \PDO
	 */
	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->repositories = array();
		$this->entitiesToUpdate = array();
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
	public function onUpdate(EntityInterface $entity)
	{
		$this->entitiesToUpdate[] = $entity;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function persist(EntityInterface $entity)
	{
		$storageProxy = $entity->_getStorageProxy();
		if (!$storageProxy)
		{
			$type = $entity->_type();
			$typeName = $type->getTypeName();
			if (!isset($this->repositories[$typeName]))
			{
				$repositoryClass = $type->getStorageRepositoryClass();
				if (!$repositoryClass)
				{
					$repositoryClass = "\\TinyCms\\NodeProvider\\Storage\\PDO\\Library\\EntityRepository";
				}
				$repository = new $repositoryClass($this->conn, $this, $type);
				$this->repositories[$typeName] = $repository;
			}
			$storageProxy = new EntityStorageProxy($this, $entity);
			$entity->_setStorageProxy($storageProxy);
			$storageProxy->updateAllFields();
		}
	}

	protected function saveEntity(EntityInterface $entity, EntityStorageProxy $storageProxy)
	{
		// recursively save related entities
		$callTypeGet = 'get';
		$type = $entity->_type();
		$fieldNames = $type->getFieldNames();
		foreach ($fieldNames as $fieldName)
		{
			if ($entity->hasFieldStorageColumn($fieldName))
			{
				$fieldType = $entity->_fieldType($fieldName);
				if ($fieldType->isEntity())
				{
					$magicGetCallName = $type->getFieldMagicCallName($fieldName, $callTypeGet);
					$fieldEntity = $entity->{$magicGetCallName}();
					$fieldStorageProxy = $fieldEntity->_getStorageProxy();
					if ($fieldStorageProxy && $fieldStorageProxy->hasUpdate())
					{
						$this->saveEntity($fieldEntity, $fieldStorageProxy);
						$fieldStorageProxy->reset();
					}
				}
			}
		}

		// save entity
		$typeName = $type->getTypeName();
		$repository = $this->repositories[$typeName];
		$repository->save($entity);
	}


	/*
	 * Writes all changes back to storage
	 */
	public function flush()
	{
		if (!empty($this->entitiesToUpdate))
		{
			foreach ($this->entitiesToUpdate as $entity)
			{
				$storageProxy = $entity->_getStorageProxy();
				if ($storageProxy->hasUpdate())
				{			
					$this->saveEntity($entity, $storageProxy);
					$storageProxy->resetUpdate();
				}
			}
			$this->entitiesToUpdate = array();
		}
	}
}