<?php

namespace TinyCms\NodeProvider\Storage\PDO\Library;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Library\EntityTypeInterface;
use TinyCms\NodeProvider\Storage\Library\EntityManagerInterface;
use TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface;

class EntityRepository implements EntityRepositoryInterface {

	/*
	 * @var PDO
	 */
	protected $conn;

	/*
	 * @var TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 */
	protected $em;

	/*
	 * @var TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	protected $type;

	/*
	 * @param $conn \PDO
	 * @param $em TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 * @param $type TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	public function __construct(\PDO $conn, EntityManagerInterface $em, EntityTypeInterface $type)
	{
		$this->em = $em;
		$this->conn = $conn;
		$this->type = $type;
	}

	/*
	 * @return TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager()
	{
		return $this->em;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	protected function _getStorageFieldNames(EntityTypeInterface $type)
	{
		$storageFieldNames = array();
		$fieldNames = $type->getFieldNames();
		foreach ($fieldNames as $fieldName)
		{
			if (!$type->isFieldReadOnly($fieldName))
			{
				if ($type->hasFieldStorageColumn($fieldName))
				{
					$storageFieldNames[] = $fieldName;
				}
			}
		}
		return $storageFieldNames;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	protected function _update(EntityInterface $entity)
	{
		$type = $entity->_type();
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	protected function _insert(EntityInterface $entity)
	{
		$callTypeGet = 'get';
		$insertValues = array();
		$type = $entity->_type();
		$insertNames = $this->_getStorageFieldNames($type);
		foreach ($insertNames as $fieldName)
		{
			$magicCallGetField = $type->getFieldMagicCallName($fieldName, $callTypeGet);
			$fieldType = $type->getFieldType($fieldName);
			if ($fieldType->isEntity())
			{
				if ($fieldType->isFieldArray($fieldName))
				{
					$insertValues[$fieldName] = array();
					$fieldEntities = $entity->{$magicCallGetField}();
					foreach ($fieldEntities as $fieldEntity)
					{
						$fieldType = $fieldEntity->getType();
						$magicCallGetId = $fieldType->getFieldMagicCallName($fieldType->getIdFieldName(), $callTypeGet);
						$insertValues[$fieldName][] = $fieldEntity->{$magicCallGetId}();
					}
				}
				else
				{
					$fieldEntity = $entity->{$magicCallGetField}();
					$fieldType = $fieldEntity->getType();
					$magicCallGetId = $fieldType->getFieldMagicCallName($fieldType->getIdFieldName(), $callTypeGet);
					$insertValues[$fieldName] = $fieldEntity->{$magicCallGetId}();
				}
			}
			elseif ($fieldType->isObject())
			{
				if ($fieldType->isFieldArray($fieldName))
				{
					$insertValues[$fieldName] = array();
					$fieldObjects = $entity->{$magicCallGetField}();
					foreach ($fieldObjects as $fieldObject)
					{
						$insertValues[$fieldName][] = $fieldType->objectToValue($fieldObject);
					}
				}
				else
				{
					$fieldObject = $entity->{$magicCallGetField}();
					$insertValues[$fieldName] = $fieldType->objectToValue($fieldObject);
				}
			}
			else
			{
				if ($fieldType->isFieldArray($fieldName))
				{
					$insertValues[$fieldName] = array();
					$entityValues = $entity->{$magicCallGetField}();
					foreach ($entityValues as $value)
					{
						$insertValues[$fieldName][] = $value;
					}
				}
				else
				{
					$insertValues[$fieldName] = $entity->{$magicCallGetField}();
				}
			}
		}
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function save(EntityInterface $entity)
	{
		$type = $entity->_type();
		$magicCallGetId = $type->getFieldMagicCallName($type->getIdFieldName(), 'get');
		$entityId = $entity->{$magicCallGetId}();
		if (null !== $entityId)
		{
			$this->_update($entity);
		}
		else
		{
			$this->_insert($entity);
		}
	}
}