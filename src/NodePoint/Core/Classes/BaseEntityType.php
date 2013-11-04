<?php

namespace NodePoint\Core\Classes;

use NodePoint\Core\Library\MagicFieldCallInfo;
use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Library\TypeFactoryInterface;
use NodePoint\Core\Library\EntityTypeInterface;

abstract class BaseEntityType extends BaseType implements EntityTypeInterface {

	/*
	 * @var NodePoint\Core\Library\TypeFactory
	 */
	protected $typeFactory;

	/*
	 * @var NodePoint\Core\Library\EntityTypeInterface
	 */
	protected $parentType;

	/*
	 * @var array indexed by fieldName
	 */
	protected $fields;

	/*
	 * @var array of string
	 */
	protected $fieldNameAliases;

	/*
	 * @var NodePoint\Core\Library\EntityInterface
	 */
	protected $staticEntity;

	/*
	 * @var array of NodePoint\Core\Library\MagicFieldCallInfo indexed by callName
	 */
	protected $magicFieldCallInfos;

	/*
	 * @var array of NodePoint\Core\Library\MagicFieldCallInfo indexed by callName
	 */
	protected $magicFieldStaticCallInfos;

	/*
	 * Constructor
	 *
	 * @param $typeName string
	 * @param $className string
	 * @param $typeFactory NodePoint\Core\Library\TypeFactoryInterface
	 * @param $typeFactory NodePoint\Core\Library\EntityTypeInterface
	 */
	protected function __construct($typeName, $className, TypeFactoryInterface $typeFactory, EntityTypeInterface $parentType=null)
	{
		$this->typeName = $typeName;
		$this->className = $className;
		$this->typeFactory = $typeFactory;
		$this->parentType = $parentType;
		$this->fields = array();
		$this->fieldNameAliases = array();
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
			if ($parentType->isTypeNameExact($typeName));
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
	 * @return boolean true if inheritance isn't possible
	 */
	final public function isFinal()
	{
		return $this->finalState;
	}

	/*
	 * @return NodePoint\Core\Library\EntityTypeInterface
	 */
	final public function getParentType()
	{
		return $this->parentType;
	}

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function setStaticEntity($entity)
	{
		$this->staticEntity = $entity;
	}

	/*
	 * @return NodePoint\Core\Library\EntityInterface
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
	 * @return array of string with fieldNames
	 */
	public function getFieldNames()
	{
		return array_keys($this->fields);
	}

	/*
	 * @return string with fieldName
	 */
	public function getFieldNameByAlias($fieldNameAlias)
	{
		if (!isset($this->fieldNameAliases[$fieldNameAlias]))
		{
			$parentType = $this->getParentType();
			if (null !== $parentType)
			{
				return $parentType->getFieldNameByAlias($fieldNameAlias);
			}
			return null;
		}
		return $this->fieldNameAliases[$fieldNameAlias];
	}

	/*
	 * @param $fieldName string
	 * @return NodePoint\Core\Library\EntityTypeFieldInfoInterface
	 */
	public function getFieldInfo($fieldName)
	{
		if (!isset($this->fields[$fieldName]))
		{
			return null;
		}
		return $this->fields[$fieldName];
	}

	/*
	 * @param $fieldName string
	 * @param $type NodePoint\Core\Library\TypeInterface
	 */
	public function setFieldType($fieldName, TypeInterface $type)
	{
		$this->fields[$fieldName] = new EntityTypeFieldInfo($fieldName, $type);
	}

	/*
	 * @param $fieldName string
	 * @return NodePoint\Core\Library\TypeInterface
	 */
	public function getFieldType($fieldName)
	{
		if (!isset($this->fields[$fieldName]))
		{ 
			return null;
		}
		return $this->fields[$fieldName]->getType();
	}

	/*
	 * @param $fieldName string
	 * @param $description array
	 */
	public function setFieldDescription($fieldName, $description)
	{
		if (!isset($this->fields[$fieldName]))
		{
			// TODO: Exception: no type for fieldName declared
			return;
		}
		$this->fields[$fieldName]->setDescription($description);
	}

	/*
	 * @param $fieldName string
	 * @return array
	 */
	public function getFieldDescription($fieldName)
	{
		if (!isset($this->fields[$fieldName]))
		{ 
			return null;
		}
		return $this->fields[$fieldName]->getDescription();
	}

	/*
	 * @param $fieldName string
	 * @param array
	 */
	public function setFieldStorageDesc($fieldName, $storageDesc)
	{
		if (!isset($this->fields[$fieldName]))
		{
			// TODO: Exception: no type for fieldName declared
			return;
		}
		$this->fields[$fieldName]->setStorageDesc($storageDesc);
	}

	/*
	 * @param $fieldName string
	 * @return array
	 */
	public function getFieldStorageDesc($fieldName)
	{
		if (!isset($this->fields[$fieldName]))
		{ 
			return null;
		}
		return $this->fields[$fieldName]->setStorageDesc();
	}

	/*
	 * @param $callName string
	 * @param $magicFieldCallInfo NodePoint\Core\Library\MagicFieldCallInfo
	 */
	public function setMagicFieldCallInfo($callName, MagicFieldCallInfo $magicFieldCallInfo)
	{
		$this->magicFieldCallInfos[$callName] = $magicFieldCallInfo;
	}

	/*
	 * @param $callName string
	 * @return NodePoint\Core\Library\MagicFieldCallInfo
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
	 * @param $magicFieldCallInfo NodePoint\Core\Library\MagicFieldCallInfo
	 */
	public function setMagicFieldStaticCallInfo($callName, MagicFieldCallInfo $magicFieldCallInfo)
	{
		$this->magicFieldStaticCallInfos[$callName] = $magicFieldCallInfo;
	}

	/*
	 * @param $callName string
	 * @return NodePoint\Core\Library\MagicFieldCallInfo
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
			$fieldInfo = $this->fields[$fieldName];
			$i18nStr = $fieldInfo->hasI18n() ? 'I18n' : '';
			$staticState = $fieldInfo->isStatic();
			$singularName = $fieldInfo->getSingularCapitalizedName();

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
			$fieldInfo->setMagicCallName('set', $setCallName);

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
			$fieldInfo->setMagicCallName('get', $getCallName);

			if ($fieldInfo->getType()->isEntity())
			{
				// entity magic get id function
				$getIdCallName = 'get' . $singularName . 'Id';
				if (!isset($this->magicFieldCallInfos[$getIdCallName]))
				{
					$magicFieldCallInfo = new MagicFieldCallInfo($fieldName, '_getMagicFieldEntityIdCall');
					$this->setMagicFieldCallInfo($getIdCallName, $magicFieldCallInfo);
					if ($staticState && !isset($this->magicFieldStaticCallInfos[$getIdCallName]))
					{
						$this->setMagicFieldStaticCallInfo($getIdCallName, $magicFieldCallInfo);
					}
				}
				$fieldInfo->setMagicCallName('getId', $getIdCallName);
			}

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
			$fieldInfo->setMagicCallName('validate', $validateCallName);

			if ($fieldInfo->isArray())
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
				$fieldInfo->setMagicCallName('cnt', $cntCallName);

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
				$fieldInfo->setMagicCallName('getitem', $getItemCallName);

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
				$fieldInfo->setMagicCallName('setitem', $setItemCallName);
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
