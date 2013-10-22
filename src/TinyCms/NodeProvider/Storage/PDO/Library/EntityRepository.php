<?php

namespace TinyCms\NodeProvider\Storage\PDO\Library;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Library\EntityTypeInterface;
use TinyCms\NodeProvider\Storage\Library\EntityManagerInterface;
use TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface;

class EntityRepository implements EntityRepositoryInterface {

	/*
	 * @var PDO
	 */
	protected $conn;

	/*
	 * @var TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 */
	protected $em;

	/*
	 * @var TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	protected $type;

	/*
	 * @param $conn \PDO
	 * @param $em TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 * @param $type TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	public function __construct(\PDO $conn, EntityManagerInterface $em, EntityTypeInterface $type)
	{
		$this->em = $em;
		$this->conn = $conn;
		$this->type = $type;
	}

	/*
	 * @return TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager()
	{
		return $this->em;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	protected function _getStorageFieldNames(EntityTypeInterface $type)
	{
		$storageFieldNames = array();
		$fieldNames = $type->getFieldNames();
		foreach ($fieldNames as $fieldName)
		{
			if (!$type->isFieldReadOnly($fieldName))
			{
				if ($type->hasFieldStorageColumn($fieldName))
				{
					$storageFieldNames[] = $fieldName;
				}
			}
		}
		return $storageFieldNames;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 * @param $saveFieldNames array of string with fieldNames
	 * @return array
	 */
	protected function _getStorageFieldValues(EntityInterface $entity, $saveFieldNames)
	{
		$callTypeGet = 'get';
		$callTypeLang = 'lang';
		$saveValues = array();
		$type = $entity->_type();
		foreach ($saveFieldNames as $fieldName)
		{
			$magicCallGetField = $type->getFieldMagicCallName($fieldName, $callTypeGet);
			if ($type->isFieldEntity($fieldName))
			{
				// get field with entity reference
				if ($type->isFieldArray($fieldName))
				{
					$saveValues[$fieldName] = array();
					$fieldEntities = $entity->{$magicCallGetField}();
					foreach ($fieldEntities as $fieldEntity)
					{
						$fieldType = $fieldEntity->_type();
						$magicCallGetId = $fieldType->getFieldMagicCallName($fieldType->getIdFieldName(), $callTypeGet);
						$saveValues[$fieldName][] = $fieldEntity->{$magicCallGetId}();
					}
				}
				else
				{
					$fieldEntity = $entity->{$magicCallGetField}();
					$fieldType = $fieldEntity->_type();
					$magicCallGetId = $fieldType->getFieldMagicCallName($fieldType->getIdFieldName(), $callTypeGet);
					$saveValues[$fieldName] = $fieldEntity->{$magicCallGetId}();
				}
			}
			else
			{
				// get field with I18n support
				if ($type->hasFieldI18n($fieldName))
				{
					$saveValues[$fieldName] = array();
					$magicCallGetLanguages = $type->getFieldMagicCallName($fieldName, $callTypeLang);
					$languages = $entity->{$magicCallGetLanguages}();
					foreach ($languages as $lang)
					{
						if ($type->isFieldArray($fieldName))
						{
							$saveValues[$fieldName][$lang] = array();
							$fieldValues = $entity->{$magicCallGetField}($lang);
							foreach ($fieldValues as $fieldValue)
							{
								if (is_object($fieldValue))
								{
									$fieldType = $type->getFieldType($fieldName);
									$fieldValue = $fieldType->objectToValue($fieldValue);
								}
								$saveValues[$fieldName][$lang][] = $fieldValue;
							}
						}
						else
						{
							$fieldValue = $entity->{$magicCallGetField}($lang);
							if (is_object($fieldValue))
							{
								$fieldType = $type->getFieldType($fieldName);
								$fieldValue = $fieldType->objectToValue($fieldValue);
							}
							$saveValues[$fieldName][$lang] = $fieldValue;
						}
					}
				}
				// get field with NO I18n support
				else
				{
					if ($type->isFieldArray($fieldName))
					{
						$saveValues[$fieldName] = array();
						$fieldValues = $entity->{$magicCallGetField}();
						foreach ($fieldValues as $fieldValue)
						{
							if (is_object($fieldValue))
							{
								$fieldType = $type->getFieldType($fieldName);
								$fieldValue = $fieldType->objectToValue($fieldValue);
							}
							$saveValues[$fieldName][] = $fieldValue;
						}
					}
					else
					{
						$fieldValue = $entity->{$magicCallGetField}();
						if (is_object($fieldValue))
						{
							$fieldType = $type->getFieldType($fieldName);
							$fieldValue = $fieldType->objectToValue($fieldValue);
						}
						$saveValues[$fieldName] = $fieldValue;
					}
				}
			}
		}
		return $saveValues;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	protected function _update(EntityInterface $entity)
	{
		$type = $entity->_type();
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	protected function _insert(EntityInterface $entity)
	{
		$callTypeGet = 'get';
		$callTypeLang = 'lang';
		$type = $entity->_type();
		$fieldNames = $this->_getStorageFieldNames($type);
		$fieldValues = $this->_getStorageFieldValues($entity, $fieldNames);
		foreach ($fieldNames as $fieldName)
		{

		}
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
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