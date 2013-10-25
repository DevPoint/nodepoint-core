<?php

namespace TinyCms\NodeProvider\Classes;

use TinyCms\NodeProvider\Library\MagicFieldCallInfo;
use TinyCms\NodeProvider\Library\TypeInterface;
use TinyCms\NodeProvider\Library\EntityTypeInterface;

abstract class BaseEntityType extends BaseType implements EntityTypeInterface {

	/*
	 * @var TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	protected $parentType;

	/*
	 * @var boolean true if inheritance isn't possible
	 */
	protected $finalState;

	/*
	 * @var string language code
	 */
	protected $defaultLanguage;

	/*
	 * @var array indexed by fieldName
	 */
	protected $fields;

	/*
	 * @var mixed string or array of string
	 */
	protected $idFieldName;

	/*
	 * @var string
	 */
	protected $aliasFieldName;

	/*
	 * @var TinyCms\NodeProvider\Library\EntityInterface
	 */
	protected $staticEntity;

	/*
	 * @var array of TinyCms\NodeProvider\Library\MagicFieldCallInfo indexed by callName
	 */
	protected $magicFieldCallInfos;

	/*
	 * @var array of TinyCms\NodeProvider\Library\MagicFieldCallInfo indexed by callName
	 */
	protected $magicFieldStaticCallInfos;

	/*
	 * @var string repositories class name
	 */
	protected $storageRepositoryClass;

	/*
	 * @var string
	 */
	protected $storageTable;

	/*
	 * Constructor
	 *
	 * @param $typeName string
	 * @param $parentType TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $description array
	 */
	protected function __construct($typeName, $parentType, $description)
	{
		$this->typeName = $typeName;
		$this->parentType = $parentType;
		$this->defaultLanguage = isset($description['defLang']) ? $description['defLang'] : 'en';
		$this->idFieldName = isset($description['idField']) ? $description['idField'] : 'id';
		$this->aliasFieldName = isset($description['aliasField']) ? $description['aliasField'] : 'alias';
		$this->storageRepositoryClass = isset($description['storageRepository']) ? $description['storageRepository'] : null;
		$this->storageTable = isset($description['storageTable']) ? $description['storageTable'] : null;
		$this->finalState = false;
		$this->fields = array();
		$this->staticEntity = new StaticEntity($this);
		$this->magicFieldCallInfos = array();
		$this->magicFieldStaticCallInfos = array();
	}

	/*
	 * @param $type string
	 * @return boolean true if type or any of the parent types matches
	 */
	final public function isTypeName($typeName)
	{
		if ($typeName == $this->getTypeName())
		{
			return true;
		}
		$parentType = $this->getParentType();
		while (null !== $parentType)
		{
			if ($typeName === $parentType->getTypeName())
			{
				return true;
			}
			$parentType = $parentType->getParentType();
		}
		return false;
	}

	/*
	 * @return boolean true for entity types
	 */
	final public function isEntity()
	{
		return true;
	}

	/*
	 * @return boolean true for object types
	 */
	final public function isObject()
	{
		return true;
	}

	/*
	 * @return string language code
	 */
	final public function getDefaultLanguage()
	{
		return $this->defaultLanguage;
	}

	/*
	 * @return boolean true if inheritance isn't possible
	 */
	final public function isFinal()
	{
		return $this->finalState;
	}

	/*
	 * @return TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	final public function getParentType()
	{
		return $this->parentType;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function setStaticEntity($entity)
	{
		$this->staticEntity = $entity;
	}

	/*
	 * @return TinyCms\NodeProvider\Library\EntityInterface
	 */
	final public function getStaticEntity()
	{
		return $this->staticEntity;
	}

	/*
	 * @param $fieldName string
	 * @return int - Int, Float, Text, Entity
	 */
	public function getStorageType()
	{
		return self::STORAGE_ENTITY;
	}

	/*
	 * @return string with repository class name
	 */
	public function getStorageRepositoryClass()
	{
		return $this->storageRepositoryClass;
	}

	/*
	 * @return string with table name
	 */
	public function getStorageTable()
	{
		return $this->storageTable;
	}

	/*
	 * @return array of string with fieldNames
	 */
	public function getFieldNames()
	{
		return array_keys($this->fields);
	}

	/*
	 * @return mixd string or array of string with id fieldName(s)
	 */
	public function getIdFieldName()
	{
		return $this->idFieldName;
	}

	/*
	 * @return string with alias fieldName(s)
	 */
	public function getAliasFieldName()
	{
		return $this->aliasFieldName;
	}

	/*
	 * @param $fieldName string
	 * @param $type TinyCms\NodeProvider\Library\TypeInterface
	 */
	public function setFieldType($fieldName, TypeInterface $type)
	{
		$this->fields[$fieldName]['type'] = $type;
	}

	/*
	 * @param $fieldName string
	 * @return TinyCms\NodeProvider\Library\TypeInterface
	 */
	public function getFieldType($fieldName)
	{
		if (!isset($this->fields[$fieldName]['type']))
		{ 
			return null;
		}
		return $this->fields[$fieldName]['type'];
	}

	/*
	 * @param $fieldName string
	 * @param $description array
	 */
	public function setFieldDescription($fieldName, $description)
	{
		$this->fields[$fieldName]['desc'] = $description;
	}

	/*
	 * @param $fieldName string
	 * @return array
	 */
	public function getFieldDescription($fieldName)
	{
		if (!isset($this->fields[$fieldName]['desc']))
		{ 
			return null;
		}
		return $this->fields[$fieldName]['desc'];
	}

	/*
	 * @param $fieldName string
	 * @return boolean if field is array
	 */
	public function isFieldArray($fieldName)
	{
		return (!empty($this->fields[$fieldName]['desc']['isArray']));
	}

	/*
	 * @param $fieldName string
	 * @return boolean if field is readOnly
	 */
	public function isFieldReadOnly($fieldName)
	{
		if (isset($this->fields[$fieldName]['desc']['readOnly']))
		{
			return $this->fields[$fieldName]['desc']['readOnly'];
		}
		if (!empty($this->fields[$fieldName]['desc']['isStatic']))
		{
			return true;
		}
		if (!empty($this->fields[$fieldName]['desc']['isConstructed']))
		{
			return true;
		}
		return false;
	}

	/*
	 * @param $fieldName string
	 * @return boolean if field has multiple translations
	 */
	public function hasFieldI18n($fieldName)
	{
		return (!empty($this->fields[$fieldName]['desc']['i18n']));
	}

	/*
	 * @param $fieldName string
	 * @return boolean if field is accessable without instance
	 */
	public function isFieldStatic($fieldName)
	{
		return (!empty($this->fields[$fieldName]['desc']['static']));
	}

	/*
	 * @param $fieldName string
	 * @return boolean if field is constructed by the values of other fields
	 */
	public function isFieldConstructed($fieldName)
	{
		return (!empty($this->fields[$fieldName]['desc']['constructed']));
	}

	/*
	 * @param $fieldName string
	 * @return boolean if field is accessable by find operations
	 */
	public function isFieldSearchable($fieldName)
	{
		return (!empty($this->fields[$fieldName]['desc']['searchable']));
	}

	/*
	 * Base field names are used for constructed
	 * fields and for fields which have dynamic options
	 *
	 * @param $fieldName string
	 * @return mixed - string or array of string with fieldNames
	 */
	public function getFieldBaseField($fieldName)
	{
		if (!isset($this->fields[$fieldName]['desc']['baseField']))
		{
			return false;
		}
		return $this->fields[$fieldName]['desc']['baseField'];
	}

	/*
	 * Calculate capitalized version of string
	 *
	 * @param string
	 * @return string
	 */
	static protected function capitalizeString($string)
	{
		return strtoupper(substr($string, 0, 1)) . substr($string, 1);
	}

	/*
	 * Retrieve or calculate fields plural name
	 * based on the fieldName
	 *
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldPluralName($fieldName)
	{
		if (!isset($this->fields[$fieldName]['desc']['plural']))
		{
			if ($this->isFieldArray($fieldName))
			{
				return $fieldName;
			}
			else
			{
				$fieldNameLen = strlen($fieldName);
				if ($fieldNameLen - 1 == strrchr($fieldName, 's'))
				{
					return $fieldName . 'es';
				}
				elseif ($fieldNameLen - 1 == strrchr($fieldName, 'y'))
				{
					return substr($fieldName, 0, $fieldNameLen-1) . 'ies';
				}
				else
				{
					return $fieldName . 's';
				}
			}
		}
		return $this->fields[$fieldName]['desc']['plural'];
	}

	/*
	 * Retrieve or calculate fields plural capitalized name
	 * based on the fieldName
	 *
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldPluralCapitalizedName($fieldName)
	{
		if (!isset($this->fields[$fieldName]['desc']['pluralCapitalize']))
		{
			$pluralName = $this->getFieldPluralName($fieldName);
			return $this->capitalizeString($pluralName);
		}
		return $this->fields[$fieldName]['desc']['pluralCapitalize'];
	}

	/*
	 * Retrieve or calculate fields singular name
	 * based on the fieldName
	 *
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldSingularName($fieldName)
	{
		if (!isset($this->fields[$fieldName]['desc']['singular']))
		{
			if (!$this->isFieldArray($fieldName))
			{
				return $fieldName;
			}
			else
			{
				$fieldNameLen = strlen($fieldName);
				if ($fieldNameLen - 2 == strrpos($fieldName, 'es'))
				{
					return substr($fieldName, 0, $fieldNameLen-2);
				}
				elseif ($fieldNameLen - 3 == strrchr($fieldName, 'ies'))
				{
					return substr($fieldName, 0, $fieldNameLen-3) . 'y';
				}
				else
				{
					return substr($fieldName, 0, $fieldNameLen-1);
				}
			}
		}
		return $this->fields[$fieldName]['desc']['singular'];
	}

	/*
	 * Retrieve or calculate fields singular capitalized name
	 * based on the fieldName
	 *
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldSingularCapitalizedName($fieldName)
	{
		if (!isset($this->fields[$fieldName]['desc']['singularCapitalize']))
		{
			$singularName = $this->getFieldSingularName($fieldName);
			return $this->capitalizeString($singularName);
		}
		return $this->fields[$fieldName]['desc']['singularCapitalize'];
	}

	/*
	 * @param $fieldName string
	 * @param $callType string  
	 *			set, get, validate, 
	 *			cnt, setitem, getitem,
	 * @return string
	 */
	public function getFieldMagicCallName($fieldName, $callType)
	{
		if (!isset($this->fields[$fieldName]['magicFncs'][$callType]))
		{
			return null;
		}
		return $this->fields[$fieldName]['magicFncs'][$callType];
	}

	/*
	 * @param $fieldName string
	 * @return boolean true if field is an Entity
	 */
	public function isFieldEntity($fieldName)
	{
		$type = $this->getFieldType($fieldName);
		if (!$type)
		{
			// TODO: Exception: no type provided for field
			return false;
		}
		return $type->isEntity();
	}

	/*
	 * @param $fieldName string
	 * @return boolean
	 */
	public function hasFieldOptions($fieldName)
	{
		return (!empty($this->fields[$fieldName]['desc']['hasOptions']));
	}

	/*
	 * @param $fieldName string
	 * @return mixed array or false
	 */
	public function getFieldOptions($fieldName)
	{
		if (!isset($this->fields[$fieldName]['desc']['options']))
		{
			return false;
		}
		return $this->fields[$fieldName]['desc']['options'];
	}

	/*
	 * @param $fieldName string
	 * @param array
	 */
	public function setFieldStorageDesc($fieldName, $storageDesc)
	{
		$this->fields[$fieldName]['storage'] = $storageDesc;
	}

	/*
	 * @param $fieldName string
	 * @return array
	 */
	public function getFieldStorageDesc($fieldName)
	{
		if (!isset($this->fields[$fieldName]['storage']))
		{ 
			return null;
		}
		return $this->fields[$fieldName]['storage'];
	}

	/*
	 * @param $fieldName string
	 * @return int - Int, Float, Text, Entity
	 */
	public function getFieldStorageType($fieldName)
	{
		if (!isset($this->fields[$fieldName]['storage']['type']))
		{ 
			return $this->getFieldType($fieldName)->getStorageType();
		}
		return $this->fields[$fieldName]['storage']['type'];
	}

	/*
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldStorageColumn($fieldName)
	{
		if (!isset($this->fields[$fieldName]['storage']['column']))
		{ 
			return null;
		}
		return $this->fields[$fieldName]['storage']['column'];
	}

	/*
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldStorageSql($fieldName)
	{
		if (!isset($this->fields[$fieldName]['storage']['sql']))
		{ 
			return null;
		}
		return $this->fields[$fieldName]['storage']['sql'];
	}

	/*
	 * @param $callName string
	 * @param $magicFieldCallInfo TinyCms\NodeProvider\Library\MagicFieldCallInfo
	 */
	public function setMagicFieldCallInfo($callName, MagicFieldCallInfo $magicFieldCallInfo)
	{
		$this->magicFieldCallInfos[$callName] = $magicFieldCallInfo;
	}

	/*
	 * @param $callName string
	 * @return TinyCms\NodeProvider\Library\MagicFieldCallInfo
	 */
	public function getMagicFieldCallInfo($callName)
	{
		if (!isset($this->magicFieldCallInfos[$callName]))
		{
			return null;
		}
		return $this->magicFieldCallInfos[$callName];
	}

	/*
	 * @param $callName string
	 * @param $magicFieldCallInfo TinyCms\NodeProvider\Library\MagicFieldCallInfo
	 */
	public function setMagicFieldStaticCallInfo($callName, MagicFieldCallInfo $magicFieldCallInfo)
	{
		$this->magicFieldStaticCallInfos[$callName] = $magicFieldCallInfo;
	}

	/*
	 * @param $callName string
	 * @return TinyCms\NodeProvider\Library\MagicFieldCallInfo
	 */
	public function getMagicFieldStaticCallInfo($callName)
	{
		if (!isset($this->magicFieldStaticCallInfos[$callName]))
		{
			return null;
		}
		return $this->magicFieldStaticCallInfos[$callName];
	}

	/*
	 * Calculate further magic function calls 
	 */
	protected function finalizeMagicCallNames()
	{
		$fieldNames = $this->getFieldNames();
		foreach ($fieldNames as $fieldName)
		{
			// required properties
			$this->fields[$fieldName]['magicFncs'] = array();
			$i18nStr = $this->hasFieldI18n($fieldName) ? 'I18n' : '';
			$staticState = $this->isFieldStatic($fieldName);
			$singularName = $this->getFieldSingularCapitalizedName($fieldName);

			// magic set function
			$setCallName = 'set' . $singularName;
			if (!isset($this->magicFieldCallInfos[$setCallName]))
			{
				$magicFieldCallInfo = new MagicFieldCallInfo($fieldName, '_setMagicFieldCall' . $i18nStr);
				if ($staticState)
				{
					if (!isset($this->magicFieldStaticCallInfos[$setCallName]))
					{
						$this->setMagicFieldStaticCallInfo($setCallName, $magicFieldCallInfo);
					}
				}
				else
				{
					$this->setMagicFieldCallInfo($setCallName, $magicFieldCallInfo);
				}
			}
			$this->fields[$fieldName]['magicFncs']['set'] = $setCallName;

			// magic get function
			$getCallName = 'get' . $singularName;
			if (!isset($this->magicFieldCallInfos[$getCallName]))
			{
				$magicFieldCallInfo = new MagicFieldCallInfo($fieldName, '_getMagicFieldCall' . $i18nStr);
				$this->setMagicFieldCallInfo($getCallName, $magicFieldCallInfo);
				if ($staticState && !isset($this->magicFieldStaticCallInfos[$getCallName]))
				{
					$this->setMagicFieldStaticCallInfo($getCallName, $magicFieldCallInfo);
				}
			}
			$this->fields[$fieldName]['magicFncs']['get'] = $getCallName;

			// magic validate function
			$validateCallName = 'validate' . $singularName;
			if (!isset($this->magicFieldCallInfos[$validateCallName]))
			{
				$magicFieldCallInfo = new MagicFieldCallInfo($fieldName, '_validateMagicFieldCall');
				$this->setMagicFieldCallInfo($validateCallName, $magicFieldCallInfo);
				if ($staticState && !isset($this->magicFieldStaticCallInfos[$validateCallName]))
				{
					$this->setMagicFieldStaticCallInfo($validateCallName, $magicFieldCallInfo);
				}
			}
			$this->fields[$fieldName]['magicFncs']['validate'] = $validateCallName;

			if ($this->isFieldArray($fieldName))
			{
				// array magic cnt function
				$cntCallName = 'get' . $singularName . 'Count';
				if (!isset($this->magicFieldCallInfos[$cntCallName]))
				{
					$magicFieldCallInfo = new MagicFieldCallInfo($fieldName, '_getMagicField' . $staticStr . 'CountCall');
					$this->setMagicFieldCallInfo($cntCallName, $magicFieldCallInfo);
					if ($staticState && !isset($this->magicFieldStaticCallInfos[$cntCallName]))
					{
						$this->setMagicFieldStaticCallInfo($cntCallName, $magicFieldCallInfo);
					}
				}
				$this->fields[$fieldName]['magicFncs']['cnt'] = $cntCallName;

				// array magic get item function
				$getItemCallName = 'get' . $singularName;
				if (!isset($this->magicFieldCallInfos[$getItemCallName]))
				{
					$magicFieldCallInfo = new MagicFieldCallInfo($fieldName, '_getMagicField' . $staticStr . 'ItemCall' . $i18nStr);
					$this->setMagicFieldCallInfo($getItemCallName, $magicFieldCallInfo);
					if ($staticState && !isset($this->magicFieldStaticCallInfos[$getItemCallName]))
					{
						$this->setMagicFieldStaticCallInfo($getItemCallName, $magicFieldCallInfo);
					}
				}
				$this->fields[$fieldName]['magicFncs']['getitem'] = $getItemCallName;

				// array magic set item function
				$setItemCallName = 'set' . $singularName;
				if (!isset($this->magicFieldCallInfos[$setItemCallName]) && empty($staticStr))
				{
					$magicFieldCallInfo = new MagicFieldCallInfo($fieldName, '_setMagicFieldItemCall' . $i18nStr);
					if ($staticState)
					{
						if (!isset($this->magicFieldStaticCallInfos[$setItemCallName]))
						{
							$this->setMagicFieldStaticCallInfo($setItemCallName, $magicFieldCallInfo);
						}
					}
					else
					{
						$this->setMagicFieldCallInfo($setItemCallName, $magicFieldCallInfo);
					}
				}
				$this->fields[$fieldName]['magicFncs']['setitem'] = $setItemCallName;
			}
		}
	}

	/*
	 * Calculate further values from the given properties
	 */
	public function finalize()
	{
		$this->finalizeMagicCallNames();
	}
}
