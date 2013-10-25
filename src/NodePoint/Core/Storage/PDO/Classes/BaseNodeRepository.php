<?php

namespace NodePoint\Core\Storage\PDO\Classes;

use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityTypeInterface;
use NodePoint\Core\Storage\Library\EntityManagerInterface;

class BaseNodeRepository extends AbstractEntityTableRepository {

	/*
	 * @param $conn \PDO
	 * @param $em NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	public function __construct(\PDO $conn, EntityManagerInterface $em)
	{
		parent::__construct($conn, $em);
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityTypeInterface
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
	 * @param $entity NodePoint\Core\Library\EntityInterface
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
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	protected function _insert(EntityInterface $entity)
	{
		// serialize the entities fields
		$type = $entity->_type();
		$fieldNames = $this->_getStorageFieldNames($type);
		$insertFieldValues = $this->_serializeEntityFields($entity, $fieldNames);

		// filter fields and insert them into the entity table
		$entityRow = array();
		$entityRow['type'] = $type->getTypeName();
		$entityTableFields = &$this->tableFields['entities'];
		foreach ($insertFieldValues as &$insertValue)
		{
			$fieldName = $insertValue['name'];
			if (isset($entityTableFields[$fieldName]))
			{
				$columnName = $entityTableFields[$fieldName];
				$entityRow[$columnName] = $insertValue['value'];
				$insertValue['done'] = true;
			}
		}
		$entityId = $this->_insertEntityRow($entityRow);
		$entity->setId($entityId);

		// filter fields and insert them into the entity fields table
		$entityFieldRows = array();
		foreach ($insertFieldValues as &$insertValue)
		{
			if (empty($insertValue['done']))
			{
				$fieldName = $insertValue['name'];
				if (isset($insertValue['items']))
				{
					$saveValue = array();
					$saveValue['name'] = $fieldName;
					$saveValue['lang'] = $insertValue['lang'];
					foreach ($insertValue['items'] as $insertItem)
					{
						$saveValue['sort'] = $insertItem['sort'];
						$saveValue['key'] = $insertItem['key'];
						$saveValue['value'] = $insertItem['value'];
						$entityFieldRows[] = $this->_getEntityFieldsTableRow($type, $entityId, $saveValue);
					}
				}
				else
				{
					$entityFieldRows[] = $this->_getEntityFieldsTableRow($type, $entityId, $insertValue);
				}
				$insertValue['done'] = true;
			}
		}
		$this->_insertEntityFieldRows($entityFieldRows);
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
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