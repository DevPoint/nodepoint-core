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
	 * @var array of NodePoint\Core\Library\MagicFieldCallInfo indexed by callName
	 */
	protected $magicFieldCallInfos;

	/*
	 * @var array
	 */
	static protected $magicFuncNames = array(
		'set' => '_setMagicFieldCall%s',
		'get' => '_getMagicFieldCall%s',
		'getid' => '_getMagicFieldEntityIdCall',
		'validate' => '_validateMagicFieldCall',
		'cnt' => '_getMagicFieldCountCall',
		'setitem' => '_setMagicFieldItemCall%s',
		'getitem' => '_getMagicFieldItemCall%s');

	/*
	 * @var array of boolean
	 */
	static protected $magicSetterTypes = array(
		'set' => true, 
		'setitem' => true);

	/*
	 * Constructor
	 *
	 * @param $typeName string
	 * @param $className string
	 * @param $typeFactory NodePoint\Core\Library\TypeFactoryInterface
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 */
	protected function __construct($typeName, $className, TypeFactoryInterface $typeFactory, EntityTypeInterface $parentType=null)
	{
		// basic properties
		$this->typeName = $typeName;
		$this->className = $className;
		$this->typeFactory = $typeFactory;
		$this->parentType = $parentType;
		$this->fields = array();
		$this->fieldNameAliases = array();
		$this->magicFieldCallInfos = array();

		// copy field infos from the parent type
		if (null !== $parentType)
		{
			$parentFieldNames = $parentType->getFieldNames();
			foreach ($parentFieldNames as $fieldName)
			{
				$fieldInfo = $parentType->getFieldInfo($fieldName);
				if (null !== $fieldInfo)
				{
					$this->fields[$fieldName] = $fieldInfo;
				}
			}
		}
	}

	/*
	 * @param $type string
	 * @return boolean true if type or any of the parent types matches
	 */
	final public function isTypeName($typeName)
	{
		if ($typeName === $this->getTypeName())
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
			return null;
		}
		return $this->fieldNameAliases[$fieldNameAlias];
	}

	/*
	 * @param $fieldName string
	 * @param $type NodePoint\Core\Library\TypeInterface
	 * @param $description array
	 * @param $storageDesc array
	 */
	public function setFieldInfo($fieldName, TypeInterface $type, $description=null, $storageDesc=null)
	{
		$fieldInfo = new EntityTypeFieldInfo($this, $fieldName, $type);
		if (null !== $description)
		{
			$fieldInfo->setDescription($description);
		}
		if (null !== $storageDesc)
		{
			$fieldInfo->setStorageDesc($storageDesc);
		}
		$this->fields[$fieldName] = $fieldInfo;
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
	 * @param array
	 */
	public function setFieldDescription($fieldName, $description)
	{
		if (!isset($this->fields[$fieldName]))
		{
			// TODO: Exception: no fieldInfo for fieldName available
			return;
		}
		$fieldInfo = $this->fields[$fieldName];
		if (!$this->isTypeNameExact($fieldInfo->getEntityTypeName()))
		{
			// TODO: Exception: write access to derived field isn't allowed
			return;
		}
		$fieldInfo->setDescription($description);
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
			// TODO: Exception: no fieldInfo for fieldName available
			return;
		}
		$fieldInfo = $this->fields[$fieldName];
		if (!$this->isTypeNameExact($fieldInfo->getEntityTypeName()))
		{
			// TODO: Exception: write access to derived field isn't allowed
			return;
		}
		$fieldInfo->setStorageDesc($storageDesc);
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
	 * Calculate further magic function calls 
	 */
	protected function _finalizeMagicCallNames()
	{
		$parentType = $this->getParentType();
		foreach ($this->fields as $fieldName => $fieldInfo)
		{
			$i18nStr = $fieldInfo->hasI18n() ? 'I18n' : '';
			foreach (self::$magicFuncNames as $callType => $magicFuncName)
			{
				$callName = $fieldInfo->getMagicCallName($callType);
				if (null !== $callName)
				{
					if (!isset($this->magicFieldCallInfos[$callName]))
					{
						$magicFieldCallInfo = (null !== $parentType) ? $parentType->getMagicFieldCallInfo($callName) : null;
						if (null === $magicFieldCallInfo)
						{
							$magicFieldCallInfo = new MagicFieldCallInfo($fieldName, sprintf($magicFuncName, $i18nStr));
						}
						$this->setMagicFieldCallInfo($callName, $magicFieldCallInfo);
					}
				}
			}
		}
	}

	/*
	 * Create mapping table for fields alias names
	 */
	protected function _finalizeFieldNameAliases()
	{
		foreach ($this->fields as $fieldName => $fieldInfo)
		{
			$alias = $fieldInfo->getNameAlias();
			if (null !== $alias)
			{
				$this->fieldNameAliases[$alias] = $fieldName;				
			}
		}
	}

	/*
	 * Calculate further values from the given properties
	 */
	public function finalize()
	{
		$this->_finalizeMagicCallNames();
		$this->_finalizeFieldNameAliases();
	}
}
