<?php

namespace NodePoint\Core\Storage\PDO\Library;

use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Storage\Library\EntityManagerInterface;
use NodePoint\Core\Storage\PDO\Library\EntityStorageProxy;
use NodePoint\Core\Storage\PDO\Classes\BaseEntityRepository;

class EntityManager implements EntityManagerInterface {

	/*
	 * @var PDO
	 */
	protected $conn;

	/*
	 * @var array of NodePoint\Core\Storage\Library\EntityRepositoryInterface
	 */
	protected $repositories;

	/*
	 * @var array of NodePoint\Core\Library\EntityInterface
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
	 * @param $entity NodePoint\Core\Library\EntityInterface
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
					$repositoryClass = "\\NodePoint\\Core\\Storage\\PDO\\Type\\Node\\NodeRepository";
				}
				$repository = new $repositoryClass($this->conn, $this);
				$this->repositories[$typeName] = $repository;
			}
			$storageProxy = new EntityStorageProxy($this, $entity);
			$entity->_setStorageProxy($storageProxy);
			$storageProxy->updateAllFields();
		}
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function update(EntityInterface $entity)
	{
		$this->entitiesToUpdate[] = $entity;
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	protected function _save(EntityInterface $entity)
	{
		// find entities which have to be saved
		// before current entity
		$callTypeGet = 'get';
		$type = $entity->_type();
		$fieldNames = $type->getFieldNames();
		$relatedEntityCanditates = array();
		foreach ($fieldNames as $fieldName)
		{
			if ($type->isFieldEntity($fieldName) && 0 != $type->getFieldStorageType($fieldName))
			{
				$magicCallGetField = $type->getFieldMagicCallName($fieldName, $callTypeGet);
				if ($type->isFieldArray($fieldName))
				{
					$relatedEntities = $entity->{$magicCallGetField}();
					if (is_array($relatedEntities))
					{
						foreach ($relatedEntities as $relatedEntity)
						{
							if (null !== $relatedEntity)
							{
								$relatedEntityCanditates[] = $relatedEntity;
							}
						}
					}
				}
				else
				{
					$relatedEntity = $entity->{$magicCallGetField}();
					if (null !== $relatedEntity)
					{
						$relatedEntityCanditates[] = $relatedEntity;
					}
				}
			}
		}

		// walk through canditates to check
		// if they should be saved in front
		foreach ($relatedEntityCanditates as $relatedEntity)
		{
			$relatedType = $relatedEntity->_type();
			$magicCallGetId = $relatedType->getFieldMagicCallName($relatedType->getIdFieldName(), $callTypeGet);
			$relatedEntityId = $relatedEntity->{$magicCallGetId}();
			if (null === $relatedEntityId)
			{
				$relatedStorageProxy = $relatedEntity->_getStorageProxy();
				if (null !== $relatedStorageProxy)
				{
					$relatedEntityManager = $relatedStorageProxy->getEntityManager();
					$relatedEntityManager->save($relatedEntity);
				}
			}
		}

		// save current entity
		$typeName = $type->getTypeName();
		$repository = $this->repositories[$typeName];
		$repository->save($entity);
	}

	/*
	 * @param $typeName string
	 * @param $entityId string
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function find($typeName, $entityId)
	{
		return null;
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function save(EntityInterface $entity)
	{
		$storageProxy = $entity->_getStorageProxy();
		if ($storageProxy && $storageProxy->hasUpdate())
		{			
			$this->_save($entity);
			$storageProxy->resetUpdate();
		}
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
				$this->save($entity);
			}
			$this->entitiesToUpdate = array();
		}
	}
}