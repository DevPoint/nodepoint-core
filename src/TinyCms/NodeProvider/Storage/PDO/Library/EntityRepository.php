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
		$result = array();
		$fieldNames = $type->getFieldNames();
		foreach ($fieldNames as $fieldName)
		{
			if (!$type->isFieldReadOnly($fieldName))
			{
				if (0 != $type->getFieldStorageType($fieldName))
				{
					$result[] = $fieldName;
				}
			}
		}
		return $result;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 * @param $fieldNames array of string with fieldNames
	 * @return array
	 */
	protected function _getStorageFieldValues(EntityInterface $entity, $fieldNames)
	{
		// explode array items
		$saveFields = array();
		$type = $entity->_type();
		$mapFieldNames = array_fill_keys($fieldNames, true);
		foreach ($entity->_fields() as $field)
		{
			$fieldName = $field->getName();
			if (!empty($mapFieldNames[$fieldName]))
			{
				if ($field->isArray())
				{
					foreach ($field->getArrayItems() as $arrayField)
					{
						$saveFields[] = array(
							'name' => $field->getName(),
							'lang' => $field->getLanguage(),
							'parent_id' => $field->getId(),
							'id' => $arrayField->getId(),
							'value' => $arrayField->getValue());
					}
				}
				else
				{
					$saveFields[] = array(
						'name' => $field->getName(),
						'lang' => $field->getLanguage(),
						'id' => $field->getId(),
						'value' => $field->getValue());
				}
			}
		}

		// convert values to storage device format
		$callTypeGet = 'get';
		foreach ($saveFields as &$saveField)
		{
			$fieldName = $saveField['name'];
			if ($type->isFieldEntity($fieldName))
			{
				$fieldEntity = $saveField['value'];
				$fieldType = $fieldEntity->_type();
				$idFieldName = $fieldType->getIdFieldName();
				$magicCallGetId = $fieldType->getFieldMagicCallName($idFieldName, $callTypeGet);
				$fieldEntityId = $fieldEntity->{$magicCallGetId}();
				$saveField['outvalue'] = $fieldEntityId;
			}
			else
			{
				$fieldValue = $saveField['value'];
				if (is_object($fieldValue))
				{
					$fieldType = $type->getFieldType($fieldName);
					$fieldValue = $fieldType->objectToValue($fieldValue);
				}
				if (is_array($fieldValue))
				{
					$fieldValue = serialize($fieldValue);
				}
				$saveField['outvalue'] = $fieldValue;
			}
		}
		return $saveFields;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	protected function _update(EntityInterface $entity)
	{
		$type = $entity->_type();
		$storageProxy = $entity->_getStorageProxy();
		$fieldNames = $storageProxy->getUpdateFieldNames();
		$updateFieldValues = $this->_getStorageFieldValues($entity, $fieldNames);
		foreach ($updateValues as &$updateValue)
		{

		}
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	protected function _insert(EntityInterface $entity)
	{
		$type = $entity->_type();
		$fieldNames = $this->_getStorageFieldNames($type);
		$insertFieldValues = $this->_getStorageFieldValues($entity, $fieldNames);
		foreach ($insertFieldValues as &$insertValue)
		{

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