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
		// prepare serialization of the entity
		$type = $entity->_type();
		$fields = $entity->_fields();
		$storageProxy = $entity->_getStorageProxy();
		$fieldNames = $storageProxy->getUpdateFieldNames();

		// filter fields and update them in the entity table
		$mapFieldNames = array_fill_keys($fieldNames, true);
		$entityId = $this->_getEntityId($entity);
		$entityRow = $this->_serializeFieldsToRow($type, $fields, $mapFieldNames, $entityId);
		if (!empty($entityRow))
		{
			$this->_updateRow($entityRow);
		}
			
		// filter fields and update them in the entity fields table
		$entityFieldRows = $this->_serializeFieldsToFieldRows($type, $fields, $mapFieldNames, $entityId);
		$this->_saveEntityFieldRows($entityFieldRows);
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	protected function _insert(EntityInterface $entity)
	{
		// prepare serialization of the entity
		$type = $entity->_type();
		$fields = $entity->_fields();
		$fieldNames = $this->_getStorageFieldNames($type);
		$mapFieldNames = array_fill_keys($fieldNames, true);

		// filter fields and insert them into the entity table
		$entityRow = $this->_serializeFieldsToRow($type, $fields, $mapFieldNames, null);
		$entityId = $this->_insertRow($entityRow);
		$this->_setEntityId($entity, $entityId);

		// filter fields and insert them into the entity fields table
		$entityFieldRows = $this->_serializeFieldsToFieldRows($type, $fields, $mapFieldNames, $entityId);
		$this->_insertEntityFieldRows($entityFieldRows);
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function save(EntityInterface $entity)
	{
		$type = $entity->_type();
		$entityId = $this->_getEntityId($entity);
		if (null !== $entityId)
		{
			$this->_update($entity);
		}
		else
		{
			$this->_insert($entity);
		}
	}

	/*
	 * @param $entityId string 
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function find($entityId)
	{
		$entityRow = $this->_findRow($entityId);
		if (null !== $entityRow)
		{
			$typeName = $entityRow['type'];
			$type = $this->em->getTypeFactory()->getType($typeName);
			$fields = $this->_unserializeFieldsFromRow($type, $entityRow);
		}
		return $entityRow;
	}
}