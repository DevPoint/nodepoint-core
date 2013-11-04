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
		$this->staticEntity = new StaticEntity($this);
		$this->magicFieldCallInfos = array();
		$this->magicFieldStaticCallInfos = array();

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
		$this->fields[$fieldName] = new EntityTypeFieldInfo($this, $fieldName, $type);
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
		$fieldInfo = $this->fields[$fieldName];
		if ($fieldInfo->getEntityTypeName() !== $this->getTypeName())
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
			// TODO: Exception: no type for fieldName declared
			return;
		}
		$fieldInfo = $this->fields[$fieldName];
		if ($fieldInfo->getEntityTypeName() !== $this->getTypeName())
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
		$parentType = $this->getParentType();
		$fieldNames = $this->getFieldNames();
		foreach ($fieldNames as $fieldName)
		{
			$fieldInfo = $this->fields[$fieldName];
			$i18nStr = $fieldInfo->hasI18n() ? 'I18n' : '';
			$staticState = $fieldInfo->isStatic();
			foreach (self::$magicFuncNames as $callType => $magicFuncName)
			{
				$callName = $fieldInfo->getMagicCallName($callType);
				if (null !== $callName)
				{
					if (!$staticState || empty(self::$magicSetterTypes[$callType]))
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
					if ($staticState && !isset($this->magicFieldStaticCallInfos[$callName]))
					{
						$magicFieldCallInfo = (null !== $parentType) ? $parentType->getMagicFieldStaticCallInfo($callName) : null;
						if (null === $magicFieldCallInfo)
						{
							$magicFieldCallInfo = new MagicFieldCallInfo($fieldName, sprintf($magicFuncName, $i18nStr));
						}
						$this->setMagicFieldStaticCallInfo($callName, $magicFieldCallInfo);
					}
				}
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
