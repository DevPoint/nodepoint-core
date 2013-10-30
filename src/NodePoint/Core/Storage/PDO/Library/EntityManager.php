<?php

namespace NodePoint\Core\Storage\PDO\Library;

use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Library\TypeFactoryInterface;
use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Storage\Library\EntityRepositoryInfo;
use NodePoint\Core\Storage\Library\EntityManagerInterface;
use NodePoint\Core\Storage\PDO\Library\EntityStorageProxy;
use NodePoint\Core\Storage\PDO\Classes\BaseEntityRepository;

class EntityManager implements EntityManagerInterface {

	/*
	 * @var PDO
	 */
	protected $conn;

	/*
	 * @var array of NodePoint\Core\Storage\Library\RepositoryInfo indexed by typeName
	 */
	protected $repositories;

	/*
	 * @var NodePoint\Core\Library\TypeFactoryInterface
	 */
	protected $typeFactory;

	/*
	 * @var array of NodePoint\Core\Library\EntityInterface
	 */
	protected $entitiesToUpdate;

	/*
	 * @param $conn PDO
	 */
	public function __construct(\PDO $conn, TypeFactoryInterface $typeFactory)
	{
		$this->conn = $conn;
		$this->typeFactory = $typeFactory;
		$this->repositories = array();
		$this->entitiesToUpdate = array();
	}

	/*
	 * @return NodePoint\Core\Library\TypeFactoryInterface
	 */
	public function getTypeFactory()
	{
		return $this->typeFactory;
	}

	/*
	 * @return PDO
	 */
	public function getConnection()
	{
		return $this->conn;
	}

	/*
	 * @param $typeName string
	 * @param $className string
	 */
	public function registerRepositoryClass($typeName, $className)
	{
		$repositoryInfo = new EntityRepositoryInfo($className, null);
		$this->repositories[$typeName] = $repositoryInfo;
	}

	/*
	 * @param $typeName string
	 * @return NodePoint\Core\Storage\Library\EntityRepositoryInterface
	 */
	public function getRepository($typeName)
	{
		if (!isset($this->repositories[$typeName]))
		{
			return null;
		}
		$repositoryInfo = $this->repositories[$typeName];
		if (null === $repositoryInfo->object)
		{
			$repositoryClass = $repositoryInfo->className;
			$repositoryInfo->object = new $repositoryClass($this->conn, $this);
		}
		return $repositoryInfo->object;
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function persist(EntityInterface $entity)
	{
		$storageProxy = $entity->_getStorageProxy();
		if (!$storageProxy)
		{
			$storageProxy = new EntityStorageProxy($this, $entity);
			$entity->_setStorageProxy($storageProxy);
			$storageProxy->updateAllFields();
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
			$idFieldName = $relatedType->getFieldNameByAlias('_id');
			$magicCallGetId = $relatedType->getFieldMagicCallName($idFieldName, $callTypeGet);
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
		$repository = $this->getRepository($typeName);
		$repository->save($entity);
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
	 * @param $typeName string
	 * @param $entityId string
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function find($typeName, $entityId)
	{
		return null;
	}
}