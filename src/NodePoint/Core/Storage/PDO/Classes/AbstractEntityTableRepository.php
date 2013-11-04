<?php

namespace NodePoint\Core\Storage\PDO\Classes;

use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityTypeInterface;
use NodePoint\Core\Classes\EntityField;
use NodePoint\Core\Classes\EntityArrayField;
use NodePoint\Core\Storage\Library\EntityManagerInterface;
use NodePoint\Core\Storage\PDO\Library\EntityRepositoryInterface;

abstract class AbstractEntityTableRepository implements EntityRepositoryInterface {

	/*
	 * @var PDO
	 */
	protected $conn;

	/*
	 * @var NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	protected $em;

	/*
	 * @var array of string with database table names
	 */
	protected $tables;

	/*
	 * @var array
	 */
	protected $tableFields;

	/*
	 * @var array
	 */
	protected $invTableFields;

	/*
	 * @var array
	 */
	protected $tableColumns;

	/*
	 * @param $conn \PDO
	 * @param $em NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	public function __construct(\PDO $conn, EntityManagerInterface $em)
	{
		// store parameters
		$this->em = $em;
		$this->conn = $conn;

		// name of database tables
		$this->tables = array(
			'entities' => 'np_entities',
			'entityFields' => 'np_entity_fields');

		// fields contained by entity table columns
		$this->tableFields = array();
		$this->tableFields['entities'] = array(
			'id' => 'id',
			'parent' => 'parent_id',
			'parentField' => 'field');

		// columns of entity table
		$this->tableColumns = array();
		$this->tableColumns['entities'] = array(
			'id' => new ColumnInfo(\PDO::PARAM_INT, 0),
			'parent_id' => new ColumnInfo(\PDO::PARAM_INT, null),
			'field' => new ColumnInfo(\PDO::PARAM_STR, ''),
			'type' => new ColumnInfo(\PDO::PARAM_STR, ''));

		// columns of entity fields table
		$this->tableColumns['entityFields'] = array(
			'id' => new ColumnInfo(\PDO::PARAM_INT, 0),
			'entity_id' => new ColumnInfo(\PDO::PARAM_STR, ''),
			'field' => new ColumnInfo(\PDO::PARAM_STR, ''),
			'type' => new ColumnInfo(\PDO::PARAM_STR, ''),
			'lang' => new ColumnInfo(\PDO::PARAM_STR, ''),
			'valueInt' => new ColumnInfo(\PDO::PARAM_INT, null),
			'valueFloat' => new ColumnInfo(\PDO::PARAM_STR, null),
			'valueText' => new ColumnInfo(\PDO::PARAM_STR, null),
			'sortIndex' => new ColumnInfo(\PDO::PARAM_INT, 0),
			'keyInt' => new ColumnInfo(\PDO::PARAM_INT, null),
			'keyText' => new ColumnInfo(\PDO::PARAM_STR, ''));

		// create inverse versions of all table fields arrays
		$this->invTableFields = array();
		foreach ($this->tableFields as $tableName => &$tableFields)
		{
			$invTableField = array();
			foreach ($tableFields as $fieldName => $column)
			{
				$invTableField[$column] = $fieldName;
			}
			$this->invTableFields[$tableName] = $invTableField;
		}
	}

	/*
	 * @return NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager()
	{
		return $this->em;
	}

	/*
	 * @param $fieldType NodePoint\Core\Library\TypeInterface
	 * @param $entityId string
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	protected function _createEntity($type, $entityId)
	{

	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 * @param $entityId string
	 */
	protected function _setEntityId($entity, $entityId)
	{
		$entityType = $entity->_type();
		$idFieldName = $entityType->getFieldNameByAlias('_id');
		$magicCallSetId = $entityType->getFieldInfo($idFieldName)->getMagicCallName('set');
		$entity->{$magicCallSetId}($entityId);
	}

	/*
	 * @param $fieldValue mixed NodePoint\Core\Library\EntityInterface or string
	 * @return string
	 */
	protected function _getEntityId($fieldValue)
	{
		if (is_object($fieldValue))
		{
			$entity = $fieldValue;
			$entityType = $entity->_type();
			$idFieldName = $entityType->getFieldNameByAlias('_id');
			$magicCallGetId = $entityType->getFieldInfo($idFieldName)->getMagicCallName('get');
			return $entity->{$magicCallGetId}();
		}
		return $fieldValue;
	}

	/*
	 * @param $fieldValue mixed NodePoint\Core\Library\EntityInterface or string
	 * @param $field NodePoint\Core\Library\EntityFieldInterface
	 * @return string
	 */
	protected function _getEntityTypeName($fieldValue, $field)
	{
		if (is_object($fieldValue))
		{
			return $fieldValue->_type()->getTypeName();
		}
		else
		{
			return $field->getTypeName();
		}
	}

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $fields array NodePoint\Core\Library\EntityFieldInterface
	 * @return array
	 */
	protected function _serializeFieldsToRow(EntityTypeInterface $type, $fields, &$mapFieldNames, $entityId)
	{
		// serialize existing fields
		$row = array();
		$entityTableFields = &$this->tableFields['entities'];
		foreach ($fields as $field)
		{
			$fieldName = $field->getName();
			if (isset($entityTableFields[$fieldName]) && !empty($mapFieldNames[$fieldName]))
			{
				$fieldType = $type->getFieldType($fieldName);
				$column = $entityTableFields[$fieldName];
				$value = $field->getValue();
				if ($fieldType->isEntity())
				{
					$value = $this->_getEntityId($value);
				}
				elseif ($fieldType->isObject())
				{
					$value = $fieldType->objectToSerialized($value);
				}
				$row[$column] = $value;
			}
		}

		// set undefined columns to its null value
		// unset field names which have been used
		$columInfos = &$this->tableColumns['entities'];
		foreach ($entityTableFields as $fieldName => $column)
		{
			if (!empty($mapFieldNames[$fieldName]))
			{
				if (!isset($row[$column]))
				{
					$row[$column] = $columInfos[$column]->nullValue;
				}
				$mapFieldNames[$fieldName] = false;
			}
		}

		// set standard fields values
		if (!empty($row))
		{
			$row['type'] = $type->getTypeName();
		}
		return $row;
	}

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $row array
	 * @return array of NodePoint\Core\Library\EntityFieldInterface
	 */
	protected function _unserializeFieldsFromRow(EntityTypeInterface $type, $row)
	{
		$fields = array();
		$invEntityTableFields = &$this->invTableFields['entities'];
		$columInfos = &$this->tableColumns['entities'];
		foreach ($columInfos as $column => $columnInfo)
		{
			if (isset($row[$column]))
			{
				$lazyLoaded = false;
				if (isset($invEntityTableFields[$column]))
				{
					$fieldName = $invEntityTableFields[$column];
					$fieldType = $type->getFieldType($fieldName);
					$value = $row[$column];
					if ($fieldType->isEntity())
					{
						$lazyLoaded = true;
					}
					elseif ($fieldType->isObject())
					{
						$value = $fieldType->objectFromSerialized($value);
					}
					$field = new EntityField($fieldName, null);
					$field->setLazyLoadState($lazyLoaded);
					$field->setValue($value);
					$fields[] = $field;
				}
			}
		}
		return $fields;
	}

	/*
	 * @param $row array
	 * @return int
	 */
	protected function _insertRow(&$row)
	{
		$columInfos = &$this->tableColumns['entities'];
		$columns = array('parent_id','field','type');
		$columnsNameStr = implode(',', $columns);
		$columnsVarStr = ':' . implode(',:', $columns);
		$stmt = $this->conn->prepare("INSERT INTO np_entities ({$columnsNameStr}) VALUES ({$columnsVarStr})");
		foreach ($columns as $column)
		{
			$stmt->bindParam(':'.$column, $row[$column], $columInfos[$column]->paramType);
		}
		$stmt->execute();
		return $this->conn->lastInsertId();
	}

	/*
	 * @param $row array
	 */
	protected function _updateRow(&$row)
	{
	}

	/*
	 * @param $entityId string
	 * @return array with entity row
	 */
	protected function _selectRow($entityId)
	{
		$columInfos = &$this->tableColumns['entities'];
		$sql = "SELECT * FROM np_entities WHERE id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(':id', $entityId, $columInfos['id']->paramType);
		$stmt->execute();
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		return $row;
	}

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $serializedField array
	 * @param $entityId int
	 * @return array
	 */
	protected function _serializedFieldToFieldRow(EntityTypeInterface $type, &$serializedField, $entityId)
	{
		$fieldName = $serializedField['name'];
		$storageType = $type->getFieldInfo($fieldName)->getStorageType();
		$columInfos = &$this->tableColumns['entityFields'];
		$fieldRow = array(
			'entity_id' => $entityId,
			'field' => $fieldName,
			'type' => $serializedField['type'],
			'lang' => isset($serializedField['lang']) ? $serializedField['lang'] : $columInfos['lang']->nullValue,
			'sortIndex' => isset($serializedField['sort']) ? $serializedField['sort'] : $columInfos['sortIndex']->nullValue,
			'keyInt' => $columInfos['keyInt']->nullValue,
			'keyText' => $columInfos['keyText']->nullValue,
			'valueInt' => $columInfos['valueInt']->nullValue,
			'valueFloat' => $columInfos['valueFloat']->nullValue,
			'valueText' => $columInfos['valueText']->nullValue);
		if (isset($serializedField['id']))
		{
			$fieldRow['id'] = $serializedField['id'];
		}
		switch ($storageType)
		{
			case TypeInterface::STORAGE_INT:
			case TypeInterface::STORAGE_ENTITY:
				$fieldRow['valueInt'] = $serializedField['value'];
				$fieldRow['keyInt'] = isset($serializedField['key']) ? $serializedField['key'] : $columInfos['keyInt']->nullValue;
				break;
			case TypeInterface::STORAGE_FLOAT:
				$fieldRow['valueFloat'] = $serializedField['value'];
				$fieldRow['keyText'] = isset($serializedField['key']) ? $serializedField['key'] : $columInfos['keyText']->nullValue;
				break;
			default:
				$fieldRow['valueText'] = $serializedField['value'];
				$fieldRow['keyText'] = isset($serializedField['key']) ? $serializedField['key'] : $columInfos['keyText']->nullValue;
				break;
		}
		return $fieldRow;
	}

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $serializedField array
	 * @param $entityId int
	 * @return array
	 */
	protected function _serializedFieldFromFieldRow(EntityTypeInterface $type, &$fieldRow)
	{
		$fieldName = $fieldRow['field'];
		$storageType = $type->getFieldInfo($fieldName)->getStorageType();
		$columInfos = &$this->tableColumns['entityFields'];
		$serializedField = array(
			'id' => $fieldRow['id'],
			'name' => $fieldName,
			'type' => $fieldRow['type'],
			'lang' => ($fieldRow['lang'] != $columInfos['lang']->nullValue) ? $fieldRow['lang'] : null,
			'sort' => ($fieldRow['sortIndex'] != $columInfos['sortIndex']->nullValue) ? $fieldRow['sortIndex'] : null);
		switch ($storageType)
		{
			case TypeInterface::STORAGE_INT:
			case TypeInterface::STORAGE_ENTITY:
				$serializedField['value'] = $fieldRow['valueInt'];
				$serializedField['key'] = ($fieldRow['keyInt'] != $columInfos['keyInt']->nullValue) ? $fieldRow['keyInt'] : null;
				break;
			case TypeInterface::STORAGE_FLOAT:
				$serializedField['value'] = $fieldRow['valueFloat'];
				$serializedField['key'] = ($fieldRow['keyText'] != $columInfos['keyText']->nullValue) ? $fieldRow['keyText'] : null;
				break;
			default:
				$serializedField['value'] = $fieldRow['valueText'];
				$serializedField['key'] = ($fieldRow['keyText'] != $columInfos['keyText']->nullValue) ? $fieldRow['keyText'] : null;
				break;
		}
		return $serializedField;
	}	

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $fields array NodePoint\Core\Library\EntityFieldInterface
	 * @param $mapFieldNames array of boolean indexed by field names
	 * @param $entityId string
	 * @return array
	 */
	protected function _serializeFieldsToFieldRows(EntityTypeInterface $type, &$fields, $mapFieldNames, $entityId)
	{
		$entityFieldRows = array();
		$entityTableFields = &$this->tableFields['entities'];
		foreach ($fields as $field)
		{
			$fieldName = $field->getName();
			if (!empty($mapFieldNames[$fieldName]))
			{
				$fieldType = $type->getFieldType($fieldName);
				$fieldSearchable = $type->getFieldInfo($fieldName)->isSearchable();
				$fieldLanguage = $field->getLanguage();
				if ($field->isArray())
				{
					foreach ($field->getArrayItems() as $arrayField)
					{
						$fieldTypeName = $fieldType->getTypeName();
						$value = $arrayField->getValue();
						if ($fieldType->isEntity())
						{
							$fieldTypeName = $this->_getEntityTypeName($value, $arrayField);
							$value = $this->_getEntityId($value);
						}
						elseif ($fieldType->isObject())
						{
							$value = $fieldType->objectToSerialized($value);
						}
						$searchKey = ($fieldSearchable) ? $fieldType->searchKeyFromValue($arrayField->getValue()) : null;
						$serializedField = array(
							'id' => $arrayField->getId(),
							'type' => $fieldTypeName,
							'name' => $fieldName,
							'lang' => $fieldLanguage,
							'sort' => $arrayField->getSortIndex(),
							'value' => $value,
							'key' => $searchKey);
						$entityFieldRows[] = $this->_serializedFieldToFieldRow($type, $serializedField, $entityId);
					}				
				}
				else
				{
					$fieldTypeName = $fieldType->getTypeName();
					$value = $field->getValue();
					if ($fieldType->isEntity())
					{
						$fieldTypeName = $this->_getEntityTypeName($value, $field);
						$value = $this->_getEntityId($value);
					}
					elseif ($fieldType->isObject())
					{
						$value = $fieldType->objectToSerialized($value);
					}
					$searchKey = ($fieldSearchable) ? $fieldType->searchKeyFromValue($field->getValue()) : null;
					$serializedField = array(
						'id' => $field->getId(),
						'type' => $fieldTypeName,
						'name' => $fieldName,
						'lang' => $fieldLanguage,
						'sort' => $field->getSortIndex(),
						'value' => $value,
						'key' => $searchKey);
					$entityFieldRows[] = $this->_serializedFieldToFieldRow($type, $serializedField, $entityId);
				}
			}
		}
		return $entityFieldRows;
	}

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $fieldRows array
	 * @return array of NodePoint\Core\Library\EntityFieldInterface
	 */
	protected function _unserializeFieldsFromFieldRows(EntityTypeInterface $type, &$fieldRows)
	{
		$fields = array();
		$hashFieldArrays = array();
		foreach ($fieldRows as &$fieldRow)
		{
			// unserialize value
			$typeName = null;
			$lazyLoaded = false;
			$serializedField = $this->_serializedFieldFromFieldRow($type, $fieldRow);
			$fieldName = $serializedField['name'];
			$fieldType = $type->getFieldType($fieldName);
			$value = $serializedField['value'];
			if ($fieldType->isEntity())
			{
				$typeName = $serializedField['type'];
				$lazyLoaded = true;
			}
			elseif ($fieldType->isObject())
			{
				$value = $fieldType->objectFromSerialized($value);
			}

			// unserialize array fields
			$fieldLanguage = $serializedField['lang'];
			if ($type->getFieldInfo($fieldName)->isArray())
			{
				// create array field item
				$field = new EntityField(null, null);
				$field->setSortIndex($serializedField['sort']);
				$field->setLazyLoadState($lazyLoaded);
				$field->setTypeName($typeName);
				$field->setValue($value);

				// find matching array field
				// or create new array field
				$crc = crc32($fieldName . $fieldLanguage);
				$fieldHash = printf("%u\n", $crc);
				if (isset($hashFieldArrays[$fieldHash]))
				{
					$foundParent = false;
					foreach ($hashFieldArrays[$fieldHash] as $fieldArray)
					{
						if ($fieldArray->getName() === $fieldName && 
							$fieldArray->getLanguage() === $fieldLanguage)
						{
							$fieldArray->addArrayItem($field);
							$foundParent = true;
						}
					}
					if (!$foundParent)
					{
						$fieldArray = new EntityArrayField($fieldName, $fieldLanguage);
						$fieldArray->addArrayItem($field);
						$hashFieldArrays[$fieldHash][] = $fieldArray;
						$fields[] = $fieldArray;
					}
				}
				else
				{
					$hashFieldArrays[$fieldHash] = array();
					$fieldArray = new EntityArrayField($fieldName, $fieldLanguage);
					$fieldArray->addArrayItem($field);
					$hashFieldArrays[$fieldHash][] = $fieldArray;
					$fields[] = $fieldArray;
				}
			}
			// unserialize single fields
			else
			{
				$field = new EntityField($fieldName, $fieldLanguage);
				$field->setSortIndex($serializedField['sort']);
				$field->setLazyLoadState($lazyLoaded);
				$field->setTypeName($typeName);
				$field->setValue($value);
				$fields[] = $field;
			}
		}

		// sort array field items
		foreach ($fields as $field)
		{
			if ($field->isArray())
			{
			// TODO: implement
			}
		}
		return $fields;
	}

	/*
	 * @param $entityId string
	 * @param $lang mixed string or array of strings
	 * @return array of fieldRows
	 */
	protected function _selectFieldRows($entityId, $lang)
	{
		$columInfos = &$this->tableColumns['entityFields'];
		$sql = "SELECT * FROM np_entity_fields WHERE entity_id = :entity_id";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(':entity_id', $entityId, $columInfos['entity_id']->paramType);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	/*
	 * @param $fieldRows array
	 */
	protected function _insertFieldRows(&$fieldRows)
	{
		$columInfos = &$this->tableColumns['entityFields'];
		$columns = array('entity_id','field','type','lang','sortIndex','valueInt','valueFloat','valueText','keyInt','keyText');
		$columnsNameStr = implode(',', $columns);
		$columnsVarStr = ':' . implode(',:', $columns);
		foreach ($fieldRows as &$fieldRow)
		{
			$stmt = $this->conn->prepare("INSERT INTO np_entity_fields ({$columnsNameStr}) VALUES ({$columnsVarStr})");
			foreach ($columns as $column)
			{
				$stmt->bindParam(':'.$column, $fieldRow[$column], $columInfos[$column]->paramType);
			}
			$stmt->execute();
		}
	}

	/*
	 * @param $fieldRows array
	 */
	protected function _saveFieldRows(&$fieldRows)
	{
		$columInfos = &$this->tableColumns['entityFields'];
		$columns = array('entity_id','field','type','lang','sortIndex','valueInt','valueFloat','valueText','keyInt','keyText');
		$columnsNameStr = implode(',', $columns);
		$columnsVarStr = ':' . implode(',:', $columns);
		foreach ($fieldRows as &$fieldRow)
		{
			// updating an existing field row
			if (isset($fieldRow['id']) && strlen($fieldRow['id']))
			{
				foreach ($columns as $column)
				{
				}
			}
			// inserting a new field row
			else
			{
				$stmt = $this->conn->prepare("INSERT INTO np_entity_fields ({$columnsNameStr}) VALUES ({$columnsVarStr})");
				foreach ($columns as $column)
				{
					$stmt->bindParam(':'.$column, $fieldRow[$column], $columInfos[$column]->paramType);
				}
				$stmt->execute();
			}
		}
	}
	
}
