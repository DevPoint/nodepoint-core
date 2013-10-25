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
				$fieldType = $type->getFieldType($fieldName);
				$fieldSearchable = $type->isFieldSearchable($fieldName);
				if ($field->isArray())
				{
					$saveFieldItems = array();
					foreach ($field->getArrayItems() as $arrayField)
					{
						$fieldValue = $arrayField->getValue();
						$saveValue = $this->_serializeValue($fieldType, $fieldValue);
						$saveSearchKey = ($fieldSearchable) ? $fieldType->searchKeyFromValue($fieldValue) : null;
						$saveFieldItems[] = array(
							'id' => $arrayField->getId(), 
							'sort' => $arrayField->getSortIndex(),
							'value' => $saveValue,
							'key' => $saveSearchKey);
					}
					$saveFields[] = array(
						'name' => $field->getName(),
						'lang' => $field->getLanguage(),
						'id' => $field->getId(),
						'items' => $saveFieldItems);
				}
				else
				{
					$fieldValue = $field->getValue();
					$saveSearchKey = ($fieldSearchable) ? $fieldType->searchKeyFromValue($fieldValue) : null;
					$saveValue = $this->_serializeValue($fieldType, $fieldValue);
					$saveFields[] = array(
						'name' => $field->getName(),
						'lang' => $field->getLanguage(),
						'id' => $field->getId(),
						'value' => $saveValue,
						'key' => $saveSearchKey);
				}
			}
		}
		return $saveFields;
	}

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $entityId int
	 * @param $saveField array
	 * @return array
	 */
	protected function _getEntityFieldsTableRow(EntityTypeInterface $type, $entityId, &$saveField)
	{
		$fieldName = $saveField['name'];
		$fieldType = $type->getFieldType($fieldName);
		$storageType = $type->getFieldStorageType($fieldName);
		$columInfo = &$this->tableColumns['entityFields'];
		$entityFieldRow = array(
			'entity_id' => $entityId,
			'fieldName' => $fieldName,
			'type' => $fieldType->getTypeName(),
			'lang' => isset($saveField['lang']) ? $saveField['lang'] : $columInfo['lang']->nullValue,
			'sortIndex' => isset($saveField['sort']) ? $saveField['sort'] : $columInfo['sortIndex']->nullValue,
			'keyInt' => $columInfo['keyInt']->nullValue,
			'keyText' => $columInfo['keyText']->nullValue,
			'valueInt' => $columInfo['valueInt']->nullValue,
			'valueFloat' => $columInfo['valueFloat']->nullValue,
			'valueText' => $columInfo['valueText']->nullValue);
		if (isset($saveField['id']))
		{
			$entityFieldRow['id'] = $saveField['id'];
		}
		switch ($storageType)
		{
			case TypeInterface::STORAGE_INT:
			case TypeInterface::STORAGE_ENTITY:
				$entityFieldRow['valueInt'] = $saveField['value'];
				$entityFieldRow['keyInt'] = isset($saveField['key']) ? $saveField['key'] : $columInfo['keyInt']->nullValue;
				break;
			case TypeInterface::STORAGE_FLOAT:
				$entityFieldRow['valueFloat'] = $saveField['value'];
				$entityFieldRow['keyText'] = isset($saveField['key']) ? $saveField['key'] : $columInfo['keyText']->nullValue;
				break;
			default:
				$entityFieldRow['valueText'] = $saveField['value'];
				$entityFieldRow['keyText'] = isset($saveField['key']) ? $saveField['key'] : $columInfo['keyText']->nullValue;
				break;
		}
		return $entityFieldRow;
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
	 * @param $entityFieldRows array
	 */
	protected function _insertEntityFieldRows(&$entityFieldRows)
	{
		$columInfo = &$this->tableColumns['entityFields'];
		$columns = array('entity_id','fieldName','type','lang','sortIndex','valueInt','valueFloat','valueText','keyInt','keyText');
		$columnsNameStr = implode(',', $columns);
		$columnsVarStr = ':' . implode(',:', $columns);
		foreach ($entityFieldRows as &$fieldRow)
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
