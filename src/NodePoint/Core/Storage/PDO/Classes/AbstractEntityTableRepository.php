<?php

namespace NodePoint\Core\Storage\PDO\Classes;

use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityTypeInterface;
use NodePoint\Core\Storage\Library\EntityManagerInterface;
use NodePoint\Core\Storage\Library\EntityRepositoryInterface;

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
	protected $entityTableFields;

	/*
	 * @var array
	 */
	protected $entityTableColumns;

	/*
	 * @var array
	 */
	protected $entityFieldTableColumns;

	/*
	 * @param $conn \PDO
	 * @param $em NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	public function __construct(\PDO $conn, EntityManagerInterface $em)
	{
		// store parameters
		$this->em = $em;
		$this->conn = $conn;

		// fields handled by entity table
		$this->entityTableFields = array(
			'id' => 'id',
			'parent' => 'parent_id');
		
		// columns for entity table
		$this->entityTableColumns = array(
			'id' => \PDO::PARAM_INT,
			'parent_id' => \PDO::PARAM_STR,
			'fieldName' => \PDO::PARAM_STR,
			'type' => \PDO::PARAM_STR);

		// columns for entity fields table
		$this->entityFieldTableColumns = array(
			'id' => \PDO::PARAM_INT,
			'entity_id' => \PDO::PARAM_STR,
			'fieldName' => \PDO::PARAM_STR,
			'type' => \PDO::PARAM_STR,
			'lang' => \PDO::PARAM_STR,
			'valueInt' => \PDO::PARAM_INT,
			'valueFloat' => \PDO::PARAM_STR,
			'valueText' => \PDO::PARAM_STR,
			'sortIndex' => \PDO::PARAM_INT,
			'keyInt' => \PDO::PARAM_INT,
			'keyText' => \PDO::PARAM_STR);
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
		$entityFieldRow = array(
			'entity_id' => $entityId,
			'fieldName' => $fieldName,
			'type' => $fieldType->getTypeName(),
			'lang' => isset($saveField['lang']) ? $saveField['lang'] : '',
			'sortIndex' => isset($saveField['sort']) ? $saveField['sort'] : 0,
			'keyInt' => null,
			'keyText' => '',
			'valueInt' => null,
			'valueFloat' => null,
			'valueText' => null);
		if (isset($saveField['id']))
		{
			$entityFieldRow['id'] = $saveField['id'];
		}
		switch ($storageType)
		{
			case TypeInterface::STORAGE_INT:
			case TypeInterface::STORAGE_ENTITY:
				$entityFieldRow['valueInt'] = $saveField['value'];
				$entityFieldRow['keyInt'] = isset($saveField['key']) ? $saveField['key'] : null;
				break;
			case TypeInterface::STORAGE_FLOAT:
				$entityFieldRow['valueFloat'] = $saveField['value'];
				$entityFieldRow['keyText'] = isset($saveField['key']) ? $saveField['key'] : '';
				break;
			default:
				$entityFieldRow['valueText'] = $saveField['value'];
				$entityFieldRow['keyText'] = isset($saveField['key']) ? $saveField['key'] : '';
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
		$stmt = $this->conn->prepare("INSERT INTO np_entities (parent_id, fieldName, type) VALUES (:parent_id, :fieldName, :type)");
		$stmt->bindParam(':parent_id', $entityRow['parent_id'], \PDO::PARAM_INT);
		$stmt->bindParam(':fieldName', $entityRow['fieldName'], \PDO::PARAM_STR);
		$stmt->bindParam(':type', $entityRow['type'], \PDO::PARAM_STR);
		$stmt->execute();
		return $this->conn->lastInsertId();
	}

	/*
	 * @param $entityFieldRows array
	 */
	protected function _insertEntityFieldRows(&$entityFieldRows)
	{
		foreach ($entityFieldRows as &$fieldRow)
		{
			$stmt = $this->conn->prepare("INSERT INTO np_entity_fields (entity_id, fieldName, type, lang, sortIndex, valueInt, valueFloat, valueText, keyInt, keyText) VALUES (:entity_id, :fieldName, :type, :lang, :sortIndex, :valueInt, :valueFloat, :valueText, :keyInt, :keyText)");
			$stmt->bindParam(':entity_id', $fieldRow['entity_id'], \PDO::PARAM_INT);
			$stmt->bindParam(':fieldName', $fieldRow['fieldName'], \PDO::PARAM_STR);
			$stmt->bindParam(':type', $fieldRow['type'], \PDO::PARAM_STR);
			$stmt->bindParam(':lang', $fieldRow['lang'], \PDO::PARAM_STR);
			$stmt->bindParam(':sortIndex', $fieldRow['sortIndex'], \PDO::PARAM_INT);
			$stmt->bindParam(':valueInt', $fieldRow['valueInt'], \PDO::PARAM_INT);
			$stmt->bindParam(':valueFloat', $fieldRow['valueFloat'], \PDO::PARAM_STR);
			$stmt->bindParam(':valueText', $fieldRow['valueText'], \PDO::PARAM_STR);
			$stmt->bindParam(':keyInt', $fieldRow['keyInt'], \PDO::PARAM_INT);
			$stmt->bindParam(':keyText', $fieldRow['keyText'], \PDO::PARAM_STR);
			$stmt->execute();
		}
	}
}
