<?php

namespace TinyCms\NodeProvider\Storage\PDO\Classes;

use TinyCms\NodeProvider\Library\TypeInterface;
use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Library\EntityTypeInterface;
use TinyCms\NodeProvider\Storage\Library\EntityManagerInterface;
use TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface;

abstract class AbstractEntityTableRepository implements EntityRepositoryInterface {

	/*
	 * @var PDO
	 */
	protected $conn;

	/*
	 * @var TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
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
	 * @param $em TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
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
	 * @return TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager()
	{
		return $this->em;
	}

	/*
	 * @param $fieldType TinyCms\NodeProvider\Library\TypeInterface
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
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
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
				if ($field->isArray())
				{
					$saveFieldItems = array();
					$fieldType = $type->getFieldType($fieldName);
					foreach ($field->getArrayItems() as $arrayField)
					{
						$fieldValue = $this->_serializeValue($fieldType, $arrayField->getValue());
						$saveFieldItems[] = array(
							'id' => $arrayField->getId(), 
							'sort' => $arrayField->getSortIndex(),
							'value' => $fieldValue);
					}
					$saveFields[] = array(
						'name' => $field->getName(),
						'lang' => $field->getLanguage(),
						'id' => $field->getId(),
						'items' => $saveFieldItems);
				}
				else
				{
					$fieldType = $type->getFieldType($fieldName);
					$fieldValue = $this->_serializeValue($fieldType, $field->getValue());
					$saveFields[] = array(
						'name' => $field->getName(),
						'lang' => $field->getLanguage(),
						'id' => $field->getId(),
						'value' => $fieldValue);
				}
			}
		}
		return $saveFields;
	}

	/*
	 * @param $entityId int
	 * @param $type TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $saveField array
	 * @return array
	 */
	protected function _getEntityFieldsTableValue($entityId, EntityTypeInterface $type, $saveField)
	{
		$fieldName = $saveField['name'];
		$fieldType = $type->getFieldType($fieldName);
		$storageType = $type->getFieldStorageType($fieldName);
		$arrFieldTableValue = array(
			'entity_id' => $entityId,
			'fieldName' => $fieldName,
			'type' => $fieldType->getTypeName(),
			'lang' => isset($saveField['lang']) ? $saveField['lang'] : '',
			'sortIndex' => isset($saveField['sort']) ? $saveField['sort'] : 0,
			'keyInt' => isset($saveField['keyInt']) ? $saveField['keyInt'] : null,
			'keyText' => isset($saveField['keyText']) ? $saveField['keyText'] : '',
			'valueInt' => null,
			'valueFloat' => null,
			'valueText' => null);
		if (isset($saveField['id']))
		{
			$arrFieldTableValue['id'] = $saveField['id'];
		}
		switch ($storageType)
		{
			case TypeInterface::STORAGE_INT:
			case TypeInterface::STORAGE_ENTITY:
				$arrFieldTableValue['valueInt'] = $saveField['value'];
				break;
			case TypeInterface::STORAGE_FLOAT:
				$arrFieldTableValue['valueFloat'] = $saveField['value'];
				break;
			default:
				$arrFieldTableValue['valueText'] = $saveField['value'];
				break;
		}
		return $arrFieldTableValue;
	}

	/*
	 * @param $entityRow array
	 * @return int
	 */
	protected function _insertEntityRow(&$entityRow)
	{
		$stmt = $this->conn->prepare("INSERT INTO tcm_entities (parent_id, fieldName, type) VALUES (:parent_id, :fieldName, :type)");
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
			$stmt = $this->conn->prepare("INSERT INTO tcm_entity_fields (entity_id, fieldName, type, lang, sortIndex, valueInt, valueFloat, valueText, keyInt, keyText) VALUES (:entity_id, :fieldName, :type, :lang, :sortIndex, :valueInt, :valueFloat, :valueText, :keyInt, :keyText)");
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
