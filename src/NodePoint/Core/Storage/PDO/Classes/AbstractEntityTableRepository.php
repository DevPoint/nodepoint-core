<?php

namespace NodePoint\Core\Storage\PDO\Classes;

use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityFieldInterface;
use NodePoint\Core\Library\EntityTypeInterface;
use NodePoint\Core\Library\EntityLazyLoadInfo;
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
			'entity' => 'np_entities',
			'entityValue' => 'np_entity_values');

		// fields contained by entity table columns
		$this->tableFields = array();
		$this->tableFields['entity'] = array(
			'id' => 'id',
			'parent' => 'parent_id',
			'parentField' => 'field');

		// columns of entity table
		$this->tableColumns = array();
		$this->tableColumns['entity'] = array(
			'id' => new ColumnInfo(\PDO::PARAM_INT, 0),
			'parent_id' => new ColumnInfo(\PDO::PARAM_INT, null),
			'field' => new ColumnInfo(\PDO::PARAM_STR, ''),
			'type' => new ColumnInfo(\PDO::PARAM_STR, ''));

		// columns of entity fields table
		$this->tableColumns['entityValue'] = array(
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
	protected function _setEntityId(EntityInterface $entity, $entityId)
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
	protected function _getEntityId(EntityInterface $entity)
	{
		$entityType = $entity->_type();
		$idFieldName = $entityType->getFieldNameByAlias('_id');
		$magicCallGetId = $entityType->getFieldInfo($idFieldName)->getMagicCallName('get');
		return $entity->{$magicCallGetId}();
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
		$entityTableFields = &$this->tableFields['entity'];
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
					if ($field->isLazyLoaded())
					{
						$lazyLoadInfo = $field->getLazyLoadInfo();
						$value = $lazyLoadInfo->entityId;
					}
					elseif (null !== $value)
					{
						$value = $this->_getEntityId($value);
					}
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
		$columInfos = &$this->tableColumns['entity'];
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
			$row['id'] = $entityId;
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
		$invEntityTableFields = &$this->invTableFields['entity'];
		$columInfos = &$this->tableColumns['entity'];
		foreach ($columInfos as $column => $columnInfo)
		{
			if (isset($row[$column]) && isset($invEntityTableFields[$column]))
			{
				$lazyLoadInfo = null;
				$fieldName = $invEntityTableFields[$column];
				$fieldType = $type->getFieldType($fieldName);
				$value = $row[$column];
				if ($fieldType->isEntity())
				{
					$lazyLoadInfo = new EntityLazyLoadInfo($value, 'Core/Node');
					$value = null;
				}
				elseif ($fieldType->isObject())
				{
					$value = $fieldType->objectFromSerialized($value);
				}
				$field = new EntityField($fieldName, null);
				$field->setValue($value);
				$field->setLazyLoadInfo($lazyLoadInfo);
				$fields[] = $field;
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
		$sqlTable = $this->tables['entity'];
		$columInfos = &$this->tableColumns['entity'];
		$columns = array('parent_id','field','type');
		$columnsNameStr = implode(',', $columns);
		$columnsVarStr = ':' . implode(',:', $columns);
		$stmt = $this->conn->prepare("INSERT INTO {$sqlTable} ({$columnsNameStr}) VALUES ({$columnsVarStr})");
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
		$commaStr = '';
		$sqlUpdate = '';
		$columInfos = &$this->tableColumns['entity'];
		$columns = array('parent_id','field','type');
		foreach ($columns as $column)
		{
			$sqlUpdate .= "{$commaStr}{$column}=:{$column}";
			$commaStr = ',';
		}
		$sqlTable = $this->tables['entity'];
		$stmt = $this->conn->prepare("UPDATE {$sqlTable} SET {$sqlUpdate} WHERE id=:id");
		foreach ($columns as $column)
		{
			$stmt->bindParam(':'.$column, $row[$column], $columInfos[$column]->paramType);
		}
		$stmt->bindParam(':id', $row['id']);
		$stmt->execute();
	}

	/*
	 * @param $entityId string
	 * @return array with entity row
	 */
	protected function _selectRow($entityId)
	{
		$sqlTable = $this->tables['entity'];
		$columInfos = &$this->tableColumns['entity'];
		$sql = "SELECT * FROM {$sqlTable} WHERE id=:id";
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
	protected function _serializedFieldToValueRow(EntityTypeInterface $type, &$serializedField, $entityId)
	{
		$fieldName = $serializedField['name'];
		$storageType = $type->getFieldInfo($fieldName)->getStorageType();
		$columInfos = &$this->tableColumns['entityValue'];
		$valueRow = array(
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
			$valueRow['id'] = $serializedField['id'];
		}
		switch ($storageType)
		{
			case TypeInterface::STORAGE_INT:
			case TypeInterface::STORAGE_ENTITY:
				$valueRow['valueInt'] = intval($serializedField['value']);
				$valueRow['keyInt'] = isset($serializedField['key']) ? intval($serializedField['key']) : $columInfos['keyInt']->nullValue;
				break;
			case TypeInterface::STORAGE_FLOAT:
				$valueRow['valueFloat'] = $serializedField['value'];
				$valueRow['keyInt'] = isset($serializedField['key']) ? $serializedField['key'] : $columInfos['keyInt']->nullValue;
				break;
			default:
				$valueRow['valueText'] = $serializedField['value'];
				$valueRow['keyText'] = isset($serializedField['key']) ? $serializedField['key'] : $columInfos['keyText']->nullValue;
				break;
		}
		return $valueRow;
	}

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $serializedField array
	 * @param $entityId int
	 * @return array
	 */
	protected function _serializedFieldFromValueRow(EntityTypeInterface $type, &$valueRow)
	{
		$fieldName = $valueRow['field'];
		$storageType = $type->getFieldInfo($fieldName)->getStorageType();
		$columInfos = &$this->tableColumns['entityValue'];
		$serializedField = array(
			'id' => $valueRow['id'],
			'name' => $fieldName,
			'type' => $valueRow['type'],
			'lang' => ($valueRow['lang'] != $columInfos['lang']->nullValue) ? $valueRow['lang'] : null,
			'sort' => ($valueRow['sortIndex'] != $columInfos['sortIndex']->nullValue) ? $valueRow['sortIndex'] : null);
		switch ($storageType)
		{
			case TypeInterface::STORAGE_INT:
			case TypeInterface::STORAGE_ENTITY:
				$serializedField['value'] = $valueRow['valueInt'];
				$serializedField['key'] = ($valueRow['keyInt'] != $columInfos['keyInt']->nullValue) ? $valueRow['keyInt'] : null;
				break;
			case TypeInterface::STORAGE_FLOAT:
				$serializedField['value'] = $valueRow['valueFloat'];
				$serializedField['key'] = ($valueRow['keyInt'] != $columInfos['keyInt']->nullValue) ? $valueRow['keyInt'] : null;
				break;
			default:
				$serializedField['value'] = $valueRow['valueText'];
				$serializedField['key'] = ($valueRow['keyText'] != $columInfos['keyText']->nullValue) ? $valueRow['keyText'] : null;
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
	protected function _serializeFieldsToValueRows(EntityTypeInterface $type, &$fields, $mapFieldNames, $entityId)
	{
		$valueRows = array();
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
							if ($arrayField->isLazyLoaded())
							{
								$lazyLoadInfo = $arrayField->getLazyLoadInfo();
								$fieldTypeName = $lazyLoadInfo->typeName;
								$value = $lazyLoadInfo->entityId;
							}
							elseif (null !== $value)
							{
								$fieldTypeName = $value->_type()->getTypeName();
								$value = $this->_getEntityId($value);
							}
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
						$valueRows[] = $this->_serializedFieldToValueRow($type, $serializedField, $entityId);
					}				
				}
				else
				{
					$fieldTypeName = $fieldType->getTypeName();
					$value = $field->getValue();
					if ($fieldType->isEntity())
					{
						if ($field->isLazyLoaded())
						{
							$lazyLoadInfo = $field->getLazyLoadInfo();
							$fieldTypeName = $lazyLoadInfo->typeName;
							$value = $lazyLoadInfo->entityId;
						}
						elseif (null !== $value)
						{
							$fieldTypeName = $value->_type()->getTypeName();
							$value = $this->_getEntityId($value);
						}
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
					$valueRows[] = $this->_serializedFieldToValueRow($type, $serializedField, $entityId);
				}
			}
		}
		return $valueRows;
	}

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $valueRows array
	 * @return array of NodePoint\Core\Library\EntityFieldInterface
	 */
	protected function _unserializeFieldsFromValueRows(EntityTypeInterface $type, &$valueRows)
	{
		$fields = array();
		$hashFieldArrays = array();
		foreach ($valueRows as &$valueRow)
		{
			// unserialize value
			$lazyLoadInfo = null;
			$serializedField = $this->_serializedFieldFromValueRow($type, $valueRow);
			$fieldName = $serializedField['name'];
			$fieldType = $type->getFieldType($fieldName);
			$value = $serializedField['value'];
			if ($fieldType->isEntity())
			{
				$lazyLoadInfo = new EntityLazyLoadInfo($value, $serializedField['type']);
				$value = null;
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
				$field->setId($serializedField['id']);
				$field->setSortIndex($serializedField['sort']);
				$field->setValue($value);
				$field->setLazyLoadInfo($lazyLoadInfo);

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
							break;
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
				$field->setId($serializedField['id']);
				$field->setSortIndex($serializedField['sort']);
				$field->setValue($value);
				$field->setLazyLoadInfo($lazyLoadInfo);
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
	protected function _selectValueRows($entityId, $lang)
	{
		$sqlTable = $this->tables['entityValue'];
		$columInfos = &$this->tableColumns['entityValue'];
		$sqlWhere = "entity_id=?";
		$params = array($entityId);
		if (!empty($lang))
		{
			$params[] = '';	// empty language code
			if (is_string($lang))
			{
				$sqlWhere .= " AND lang IN(?,?)";
				$params[] = $lang;
			}
			else if (is_array($lang))
			{
				$langCount = count($lang) + 1;
				$sqlLang = str_repeat("?,", $langCount - 1) . "?";
				$sqlWhere .= " AND lang IN({$sqlLang})";
				foreach ($lang as $langItem)
				{
					$params[] = $langItem;
				}
			}
		}
		$stmt = $this->conn->prepare("SELECT * FROM {$sqlTable} WHERE {$sqlWhere}");
		$stmt->execute($params);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	/*
	 * @param $valueRows array
	 */
	protected function _saveValueRows(&$valueRows)
	{
		// updating an existing field row
		$sqlTable = $this->tables['entityValue'];
		$columInfos = &$this->tableColumns['entityValue'];
		$columns = array('entity_id','field','type','lang','sortIndex','valueInt','valueFloat','valueText','keyInt','keyText');
		foreach ($valueRows as &$valueRow)
		{
			if (!empty($valueRow['id']))
			{
				$commaStr = '';
				$sqlUpdate = '';
				foreach ($columns as $column)
				{
					$sqlUpdate .= "{$commaStr}{$column}=:{$column}";
					$commaStr = ',';
				}
				$stmt = $this->conn->prepare("UPDATE {$sqlTable} SET {$sqlUpdate} WHERE id=:id");
				foreach ($columns as $column)
				{
					$stmt->bindParam(':'.$column, $valueRow[$column], $columInfos[$column]->paramType);
				}
				$stmt->bindParam(':id', $valueRow['id']);
				$stmt->execute();
			}
		}

		// inserting new field rows
		$columnsNameStr = implode(',', $columns);
		$columnsVarStr = ':' . implode(',:', $columns);
		foreach ($valueRows as &$valueRow)
		{
			if (empty($valueRow['id']))
			{
				$stmt = $this->conn->prepare("INSERT INTO {$sqlTable} ({$columnsNameStr}) VALUES ({$columnsVarStr})");
				foreach ($columns as $column)
				{
					$stmt->bindParam(':'.$column, $valueRow[$column], $columInfos[$column]->paramType);
				}
				$stmt->execute();
			}
		}
	}
	
}
