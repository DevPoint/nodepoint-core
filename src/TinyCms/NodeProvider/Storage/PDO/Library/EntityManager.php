<?php

namespace TinyCms\NodeProvider\Storage\PDO\Library;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Storage\Library\EntityManagerInterface;
use TinyCms\NodeProvider\Storage\PDO\Library\EntityStorageProxy;
use TinyCms\NodeProvider\Storage\PDO\Serialize;

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
	 * @var array of TinyCms\NodeProvider\Storage\Library\SerializerInterface
	 */
	protected $serializers;

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

		// create standard serializers
		$this->serializers = array();
		$stringSerializer = new Serialize\StringSerializer();
		$this->serializers['TinyCmsCore/String'] = $stringSerializer;
		$this->serializers['TinyCmsCore/Alias'] = $stringSerializer;
		$this->serializers['TinyCmsCore/Text'] = $stringSerializer;
		$this->serializers['TinyCmsCore/RichText'] = $stringSerializer;

		$arraySerializer = new Serialize\ArraySerializer();
		$this->serializers['TinyCmsCore/Position2d'] = $arraySerializer;
		$this->serializers['TinyCmsCore/Bound2d'] = $arraySerializer;
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
	protected function _save(EntityInterface $entity)
	{
		// find entities which are necessary to
		// save before current entity
		$callTypeGet = 'get';
		$type = $entity->_type();
		$fieldNames = $type->getFieldNames();
		$relatedEntityCanditates = array();
		foreach ($fieldNames as $fieldName)
		{
			if ($type->isFieldEntity($fieldName) && $type->hasFieldStorageColumn($fieldName))
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
	 * @param $serializer TinyCms\NodeProvider\Storage\Library\SerializerInterface
	 */
	public function setSerializer($typeName, SerializerInterface $serializer)
	{
		$this->serializers[$typeName] = $serializer;
	}

	/*
	 * @param $typeName string
	 * @return TinyCms\NodeProvider\Storage\Library\SerializerInterface
	 */
	public function getSerializer($typeName)
	{
		if (!isset($this->serializers[$typeName]))
		{
			return null;
		}
		return $this->serializers[$typeName];
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
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