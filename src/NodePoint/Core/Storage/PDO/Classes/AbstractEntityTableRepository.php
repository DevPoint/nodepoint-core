<?php

namespace NodePoint\Core\Storage\PDO\Classes;

use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityTypeInterface;
use NodePoint\Core\Classes\EntityField;
use NodePoint\Core\Classes\EntityArrayField;
use NodePoint\Core\Storage\Library\EntityManagerInterface;
use NodePoint\Core\Storage\Library\EntityRepositoryInterface;
use NodePoint\Core\Storage\PDO\Library\PDOColumnInfo;

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

		// fields contained by entity table columns
		$this->tableFields = array();
		$this->tableFields['entities'] = array(
			'id' => 'id',
			'parent' => 'parent_id',
			'parentField' => 'field');

		// columns of entity table
		$this->tableColumns = array();
		$this->tableColumns['entities'] = array(
			'id' => new PDOColumnInfo(\PDO::PARAM_INT, 0),
			'parent_id' => new PDOColumnInfo(\PDO::PARAM_INT, null),
			'field' => new PDOColumnInfo(\PDO::PARAM_STR, ''),
			'type' => new PDOColumnInfo(\PDO::PARAM_STR, ''));

		// columns of entity fields table
		$this->tableColumns['entityFields'] = array(
			'id' => new PDOColumnInfo(\PDO::PARAM_INT, 0),
			'entity_id' => new PDOColumnInfo(\PDO::PARAM_STR, ''),
			'field' => new PDOColumnInfo(\PDO::PARAM_STR, ''),
			'type' => new PDOColumnInfo(\PDO::PARAM_STR, ''),
			'lang' => new PDOColumnInfo(\PDO::PARAM_STR, ''),
			'valueInt' => new PDOColumnInfo(\PDO::PARAM_INT, null),
			'valueFloat' => new PDOColumnInfo(\PDO::PARAM_STR, null),
			'valueText' => new PDOColumnInfo(\PDO::PARAM_STR, null),
			'sortIndex' => new PDOColumnInfo(\PDO::PARAM_INT, 0),
			'keyInt' => new PDOColumnInfo(\PDO::PARAM_INT, null),
			'keyText' => new PDOColumnInfo(\PDO::PARAM_STR, ''));

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
		$magicCallSetId = $entityType->getFieldMagicCallName($idFieldName, 'set');
		$entity->{$magicCallSetId}($entityId);
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 * @return string
	 */
	protected function _getEntityId($fieldValue)
	{
		if (is_object($fieldValue))
		{
			$entity = $fieldValue;
			$entityType = $entity->_type();
			$idFieldName = $entityType->getFieldNameByAlias('_id');
			$magicCallGetId = $entityType->getFieldMagicCallName($idFieldName, 'get');
			return $entity->{$magicCallGetId}();
		}
		return $fieldValue;
	}

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $fields array NodePoint\Core\Library\EntityFieldInterface
	 * @return array
	 */
	protected function _serializeFieldsToEntityRow(EntityTypeInterface $type, $fields, &$mapFieldNames, $entityId)
	{
		// serialize existing fields
		$entityRow = array();
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
				$entityRow[$column] = $value;
			}
		}

		// set undefined columns to its null value
		// unset field names which have been used
		$columInfos = &$this->tableColumns['entities'];
		foreach ($entityTableFields as $fieldName => $column)
		{
			if (!empty($mapFieldNames[$fieldName]))
			{
				if (!isset($entityRow[$column]))
				{
					$entityRow[$column] = $columInfos[$column]->nullValue;
				}
				$mapFieldNames[$fieldName] = false;
			}
		}

		// set standard fields values
		if (!empty($entityRow))
		{
			$entityRow['type'] = $type->getTypeName();
		}
		return $entityRow;
	}

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $entityRow array
	 * @return array of NodePoint\Core\Library\EntityFieldInterface
	 */
	protected function _unserializeFieldsFromEntityRow(EntityTypeInterface $type, $entityRow)
	{
		$fields = array();
		$invEntityTableFields = &$this->invTableFields['entities'];
		$columInfos = &$this->tableColumns['entities'];
		foreach ($columInfos as $column => $columnInfo)
		{
			if (isset($entityRow[$column]))
			{
				$fieldName = $invEntityTableFields[$column];
				$value = $entityRow[$column];
				if ($fieldType->isObject())
				{
					$value = $fieldType->objectFromSerialized($value);
				}
				$field = new EntityField($fieldName, null);
				$field->setValue($value);
				$fields[] = $field;
			}
		}
		return $fields;
	}

	/*
	 * @param $entityRow array
	 * @return int
	 */
	protected function _insertEntityRow(&$entityRow)
	{
		$columInfos = &$this->tableColumns['entities'];
		$columns = array('parent_id','field','type');
		$columnsNameStr = implode(',', $columns);
		$columnsVarStr = ':' . implode(',:', $columns);
		$stmt = $this->conn->prepare("INSERT INTO np_entities ({$columnsNameStr}) VALUES ({$columnsVarStr})");
		foreach ($columns as $column)
		{
			$stmt->bindParam(':'.$column, $entityRow[$column], $columInfos[$column]->paramType);
		}
		$stmt->execute();
		return $this->conn->lastInsertId();
	}

	/*
	 * @param $entityRow array
	 */
	protected function _updateEntityRow(&$entityRow)
	{
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
		$storageType = $type->getFieldStorageType($fieldName);
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
		$fieldName = $serializedField['name'];
		$storageType = $type->getFieldStorageType($fieldName);
		$columInfos = &$this->tableColumns['entityFields'];
		$serializedField = array(
			'id' => $fieldRow['id'],
			'name' => $fieldName,
			'type' => $fieldRow['lang'],
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
				$fieldSearchable = $type->isFieldSearchable($fieldName);
				$fieldLanguage = $field->getLanguage();
				if ($field->isArray())
				{
					foreach ($field->getArrayItems() as $arrayField)
					{
						$value = $arrayField->getValue();
						if ($fieldType->isEntity())
						{
							$value = $this->_getEntityId($value);
						}
						elseif ($fieldType->isObject())
						{
							$value = $fieldType->objectToSerialized($value);
						}
						$searchKey = ($fieldSearchable) ? $fieldType->searchKeyFromValue($arrayField->getValue()) : null;
						$serializedField = array(
							'id' => $arrayField->getId(),
							'type' => $fieldType->getTypeName(),
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
					$value = $field->getValue();
					if ($fieldType->isEntity())
					{
						$value = $this->_getEntityId($value);
					}
					elseif ($fieldType->isObject())
					{
						$value = $fieldType->objectToSerialized($value);
					}
					$searchKey = ($fieldSearchable) ? $fieldType->searchKeyFromValue($field->getValue()) : null;
					$serializedField = array(
						'id' => $field->getId(),
						'type' => $fieldType->getTypeName(),
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
		foreach ($fieldRows as $fieldRow)
		{
			// unserialize value
			$lazyLoaded = false;
			$serializedField = $this->_serializedFieldFromFieldRow($type, $fieldRow);
			$value = $serializedField['value'];
			if ($fieldType->isEntity())
			{
				$lazyLoaded = true;
			}
			if ($fieldType->isObject())
			{
				$value = $fieldType->objectFromSerialized($value);
			}

			// unserialize array fields
			$fieldName = $serializedField['name'];
			$fieldLanguage = $serializedField['lang'];
			if ($type->isFieldArray())
			{
				// create array field item
				$field = new EntityField(null, null);
				$field->setSortIndex($serializedField['sort']);
				$field->setLazyLoadState($lazyLoaded);
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
	 * @param $fieldRows array
	 */
	protected function _insertEntityFieldRows(&$fieldRows)
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
	protected function _saveEntityFieldRows(&$fieldRows)
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
