<?php

namespace NodePoint\Core\Storage\PDO\Classes;

use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityTypeInterface;
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

		// fields contained by entity table
		$this->tableFields = array();
		$this->tableFields['entities'] = array(
			'id' => 'id',
			'parent' => 'parent_id');

		// columns of entity table
		$this->tableColumns = array();
		$this->tableColumns['entities'] = array(
			'id' => new PDOColumnInfo(\PDO::PARAM_INT, 0),
			'parent_id' => new PDOColumnInfo(\PDO::PARAM_INT, null),
			'fieldName' => new PDOColumnInfo(\PDO::PARAM_STR, ''),
			'type' => new PDOColumnInfo(\PDO::PARAM_STR, ''));

		// columns of entity fields table
		$this->tableColumns['entityFields'] = array(
			'id' => new PDOColumnInfo(\PDO::PARAM_INT, 0),
			'entity_id' => new PDOColumnInfo(\PDO::PARAM_STR, ''),
			'fieldName' => new PDOColumnInfo(\PDO::PARAM_STR, ''),
			'type' => new PDOColumnInfo(\PDO::PARAM_STR, ''),
			'lang' => new PDOColumnInfo(\PDO::PARAM_STR, ''),
			'valueInt' => new PDOColumnInfo(\PDO::PARAM_INT, null),
			'valueFloat' => new PDOColumnInfo(\PDO::PARAM_STR, null),
			'valueText' => new PDOColumnInfo(\PDO::PARAM_STR, null),
			'sortIndex' => new PDOColumnInfo(\PDO::PARAM_INT, 0),
			'keyInt' => new PDOColumnInfo(\PDO::PARAM_INT, null),
			'keyText' => new PDOColumnInfo(\PDO::PARAM_STR, ''));
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
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 * @param $fieldNames array of string with fieldNames
	 * @return array
	 */
	protected function _serializeFields(EntityInterface $entity, $fieldNames)
	{
		$serializedFields = array();
		$type = $entity->_type();
		$mapFieldNames = array_fill_keys($fieldNames, true);
		foreach ($entity->_fields() as $field)
		{
			$fieldName = $field->getName();
			if (!empty($mapFieldNames[$fieldName]))
			{
				$fieldType = $type->getFieldType($fieldName);
				$fieldSearchable = $type->isFieldSearchable($fieldName);
				if ($field->isArray())
				{
					$serializedFieldItems = array();
					foreach ($field->getArrayItems() as $arrayField)
					{
						$fieldValue = $arrayField->getValue();
						$serializedValue = $this->_serializeValue($fieldType, $fieldValue);
						$searchKey = ($fieldSearchable) ? $fieldType->searchKeyFromValue($fieldValue) : null;
						$serializedFieldItems[] = array(
							'id' => $arrayField->getId(), 
							'sort' => $arrayField->getSortIndex(),
							'value' => $serializedValue,
							'key' => $searchKey);
					}
					$serializedFields[] = array(
						'id' => $field->getId(),
						'name' => $field->getName(),
						'lang' => $field->getLanguage(),
						'items' => $serializedFieldItems);
				}
				else
				{
					$fieldValue = $field->getValue();
					$searchKey = ($fieldSearchable) ? $fieldType->searchKeyFromValue($fieldValue) : null;
					$serializedValue = $this->_serializeValue($fieldType, $fieldValue);
					$serializedFields[] = array(
						'name' => $field->getName(),
						'lang' => $field->getLanguage(),
						'id' => $field->getId(),
						'value' => $serializedValue,
						'key' => $searchKey);
				}
			}
		}
		return $serializedFields;
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
		$fieldType = $type->getFieldType($fieldName);
		$storageType = $type->getFieldStorageType($fieldName);
		$columInfo = &$this->tableColumns['entityFields'];
		$fieldRow = array(
			'entity_id' => $entityId,
			'fieldName' => $fieldName,
			'type' => $fieldType->getTypeName(),
			'lang' => isset($serializedField['lang']) ? $serializedField['lang'] : $columInfo['lang']->nullValue,
			'sortIndex' => isset($serializedField['sort']) ? $serializedField['sort'] : $columInfo['sortIndex']->nullValue,
			'keyInt' => $columInfo['keyInt']->nullValue,
			'keyText' => $columInfo['keyText']->nullValue,
			'valueInt' => $columInfo['valueInt']->nullValue,
			'valueFloat' => $columInfo['valueFloat']->nullValue,
			'valueText' => $columInfo['valueText']->nullValue);
		if (isset($serializedField['id']))
		{
			$fieldRow['id'] = $serializedField['id'];
		}
		switch ($storageType)
		{
			case TypeInterface::STORAGE_INT:
			case TypeInterface::STORAGE_ENTITY:
				$fieldRow['valueInt'] = $serializedField['value'];
				$fieldRow['keyInt'] = isset($serializedField['key']) ? $serializedField['key'] : $columInfo['keyInt']->nullValue;
				break;
			case TypeInterface::STORAGE_FLOAT:
				$fieldRow['valueFloat'] = $serializedField['value'];
				$fieldRow['keyText'] = isset($serializedField['key']) ? $serializedField['key'] : $columInfo['keyText']->nullValue;
				break;
			default:
				$fieldRow['valueText'] = $serializedField['value'];
				$fieldRow['keyText'] = isset($serializedField['key']) ? $serializedField['key'] : $columInfo['keyText']->nullValue;
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
		$fieldType = $type->getFieldType($fieldName);
		$storageType = $type->getFieldStorageType($fieldName);
		$columInfo = &$this->tableColumns['entityFields'];
		$serializedField = array(
			'id' => $fieldRow['id'],
			'name' => $fieldName,
			'lang' => ($fieldRow['lang'] != $columInfo['lang']->nullValue) ? $fieldRow['lang'] : null);
		switch ($storageType)
		{
			case TypeInterface::STORAGE_INT:
			case TypeInterface::STORAGE_ENTITY:
				$serializedField['value'] = $fieldRow['valueInt'];
				$serializedField['key'] = ($fieldRow['keyInt'] != $columInfo['keyInt']->nullValue) ? $fieldRow['keyInt'] : null;
				break;
			case TypeInterface::STORAGE_FLOAT:
				$serializedField['value'] = $fieldRow['valueFloat'];
				$serializedField['key'] = ($fieldRow['keyText'] != $columInfo['keyText']->nullValue) ? $fieldRow['keyText'] : null;
				break;
			default:
				$serializedField['value'] = $fieldRow['valueText'];
				$serializedField['key'] = ($fieldRow['keyText'] != $columInfo['keyText']->nullValue) ? $fieldRow['keyText'] : null;
				break;
		}
		return $serializedField;
	}	

	/*
	 * @param $entityRow array
	 * @return int
	 */
	protected function _insertEntityRow(&$entityRow)
	{
		$columInfo = &$this->tableColumns['entities'];
		$columns = array('parent_id','fieldName','type');
		$columnsNameStr = implode(',', $columns);
		$columnsVarStr = ':' . implode(',:', $columns);
		$stmt = $this->conn->prepare("INSERT INTO np_entities ({$columnsNameStr}) VALUES ({$columnsVarStr})");
		foreach ($columns as $column)
		{
			$stmt->bindParam(':'.$column, $entityRow[$column], $columInfo[$column]->paramType);
		}
		$stmt->execute();
		return $this->conn->lastInsertId();
	}

	/*
	 * @param $fieldRows array
	 */
	protected function _insertEntityFieldRows(&$fieldRows)
	{
		$columInfo = &$this->tableColumns['entityFields'];
		$columns = array('entity_id','fieldName','type','lang','sortIndex','valueInt','valueFloat','valueText','keyInt','keyText');
		$columnsNameStr = implode(',', $columns);
		$columnsVarStr = ':' . implode(',:', $columns);
		foreach ($fieldRows as &$fieldRow)
		{
			$stmt = $this->conn->prepare("INSERT INTO np_entity_fields ({$columnsNameStr}) VALUES ({$columnsVarStr})");
			foreach ($columns as $column)
			{
				$stmt->bindParam(':'.$column, $fieldRow[$column], $columInfo[$column]->paramType);
			}
			$stmt->execute();
		}
	}
}
