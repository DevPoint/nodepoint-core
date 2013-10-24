<?php

namespace TinyCms\NodeProvider\Storage\PDO\Classes;

use TinyCms\NodeProvider\Library\TypeInterface;
use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Library\EntityTypeInterface;
use TinyCms\NodeProvider\Storage\Library\EntityManagerInterface;

class BaseNodeRepository extends AbstractEntityTableRepository {

	/*
	 * @param $conn \PDO
	 * @param $em TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 */
	public function __construct(\PDO $conn, EntityManagerInterface $em)
	{
		parent::__construct($conn, $em);
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @return array of fieldNames
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
		// serialize the entities fields
		$type = $entity->_type();
		$fieldNames = $this->_getStorageFieldNames($type);
		$insertFieldValues = $this->_serializeEntityFields($entity, $fieldNames);

		// filter fields and insert them into the entity table
		$mapFieldsSaved = array();
		$entityRow = array();
		$entityRow['type'] = $type->getTypeName();
		foreach ($insertFieldValues as &$insertValue)
		{
			$fieldName = $insertValue['name'];
			if (isset($this->entityTableFields[$fieldName]))
			{
				$columnName = $this->entityTableFields[$fieldName];
				$entityTableValue[$columnName] = $insertValue['value'];
				$mapFieldsSaved[$fieldName] = true;
			}
		}
		$entityId = $this->_insertEntityRow($entityRow);
		$entity->setId($entityId);

		// filter fields and insert them into the entity fields table
		$entityFieldRows = array();
		foreach ($insertFieldValues as &$insertValue)
		{
			$fieldName = $insertValue['name'];
			if (empty($mapFieldsSaved[$fieldName]))
			{
				if (isset($insertValue['items']))
				{
					$saveValue = array();
					$saveValue['name'] = $fieldName;
					$saveValue['lang'] = $insertValue['lang'];
					foreach ($insertValue['items'] as $insertItem)
					{
						$saveValue['sort'] = $insertItem['sort'];
						$saveValue['value'] = $insertItem['value'];
						$entityFieldRows[] = $this->_getEntityFieldsTableValue($entityId, $type, $saveValue);
					}
				}
				else
				{
					$entityFieldRows[] = $this->_getEntityFieldsTableValue($entityId, $type, $insertValue);
				}
				$mapFieldsSaved[$fieldName] = true;
			}
		}
		$this->_insertEntityFieldRows($entityFieldRows);
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