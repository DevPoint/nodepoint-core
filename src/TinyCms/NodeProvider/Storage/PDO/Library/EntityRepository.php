<?php

namespace TinyCms\NodeProvider\Storage\PDO\Library;

use TinyCms\NodeProvider\Library\TypeInterface;
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
	 * @param $fieldType TinyCms\NodeProvider\Library\TypeInterface
	 * @param $value mixed
	 * @return mixed string, int, float
	 */
	protected function _serializeValue(TypeInterface $type, $value)
	{
		if ($type->isEntity())
		{
			$entityType = $value->_type();
			$idFieldName = $entityType->getIdFieldName();
			$magicCallGetId = $entityType->getFieldMagicCallName($idFieldName, 'get');
			$value = $value->{$magicCallGetId}();
		}
		else if ($type->isObject())
		{
			$value = $type->objectToSerialized($value);
		}
		return $value;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 * @param $fieldNames array of string with fieldNames
	 * @return array
	 */
	protected function _serializeEntityFields(EntityInterface $entity, $fieldNames)
	{
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
					$saveFieldItems = array();
					$fieldType = $type->getFieldType($fieldName);
					foreach ($field->getArrayItems() as $arrayField)
					{
						$fieldValue = $this->_serializeValue($fieldType, $arrayField->getValue());
						$saveFieldItems[] = array(
							'id' => $arrayField->getId(), 
							'value' => $fieldValue);
					}
					$saveFields[] = array(
						'name' => $field->getName(),
						'lang' => $field->getLanguage(),
						'id' => $field->getId(),
						'items' => $saveFieldItems);
				}
				else
				{
					$fieldType = $type->getFieldType($fieldName);
					$fieldValue = $this->_serializeValue($fieldType, $field->getValue());
					$saveFields[] = array(
						'name' => $field->getName(),
						'lang' => $field->getLanguage(),
						'id' => $field->getId(),
						'value' => $fieldValue);
				}
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
		$updateFieldValues = $this->_serializeEntityFields($entity, $fieldNames);
		foreach ($updateFieldValues as &$updateValue)
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
		$insertFieldValues = $this->_serializeEntityFields($entity, $fieldNames);
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