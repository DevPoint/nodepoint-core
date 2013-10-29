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
		$mapFieldNames = array_fill_keys($fieldNames, true);

		// filter fields and update them intthe entity table
		$entityRow = $this->_serializeFieldsToEntityRow($type, $fields, $mapFieldNames);
		$this->_updateEntityRow($entityRow);
		
		// filter fields and update them in the entity fields table
		$magicCallGetId = $type->getFieldMagicCallName($type->getIdFieldName(), 'get');
		$entityId = $entity->{$magicCallGetId}();
		$entityFieldRows = $this->_serializeFieldsToFieldRows($type, $fields, $mapFieldNames, $entityId);
		$this->_updateEntityFieldRows($entityFieldRows);
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
		$entityRow = $this->_serializeFieldsToEntityRow($type, $fields, $mapFieldNames);
		$entityId = $this->_insertEntityRow($entityRow);
		$magicCallSetId = $type->getFieldMagicCallName($type->getIdFieldName(), 'set');
		$entity->{$magicCallSetId}($entityId);

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

	/*
	 * @param $entityId string 
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function find($entityId)
	{




	}
}