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
	 * @var array of TinyCms\NodeProvider\Library\MagicFieldCallInfo indexed by callName
	 */
	protected $magicFieldCallInfos;


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
		$this->finalState = false;
		$this->fields = array();
		$this->magicFieldCallInfos = array();
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
	 * @return boolean
	 */
	final public function isObject()
	{
		return true;
	}

	/*
	 * @return boolean
	 */
	final public function isEntity()
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
	 * @return boolean
	 */
	public function isFieldArray($fieldName)
	{
		if (empty($this->fields[$fieldName]['desc']['isArray']))
		{ 
			return false;
		}
		return true;
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
		return (!empty($this->fields[$fieldName]['desc']['isStatic']));
	}

	/*
	 * @param $fieldName string
	 * @param $value mixed
	 */
	public function setFieldStaticValue($fieldName, $value)
	{
		if ($this->hasFieldI18n($fieldName))
		{
			// TODO: Exception: use i18n version of function
			return;
		}
		$this->fields[$fieldName]['staticValues'] = $value;
	}

	/*
	 * @param $fieldName string
	 * @return mixed
	 */
	public function getFieldStaticValue($fieldName)
	{
		if ($this->hasFieldI18n($fieldName))
		{
			// TODO: Exception: use i18n version of function
			return null;
		}
		if (!isset($this->fields[$fieldName]['staticValues']))
		{
			return null;
		}
		return $this->fields[$fieldName]['staticValues'];
	}

	/*
	 * @param $fieldName string
	 * @param $lang string with language code
	 * @param $value mixed
	 */
	public function setFieldStaticValueI18n($fieldName, $lang, $value)
	{
		if (!$this->hasFieldI18n($fieldName))
		{
			// TODO: Exception: language access to field, which hasn't i18n
			return;
		}
		if (!isset($this->fields[$fieldName]['staticValues']))
		{
			$this->fields[$fieldName]['staticValues'] = array();
		}
		$this->fields[$fieldName]['staticValues'][$lang] = $value;
	}

	/*
	 * @param $fieldName string
	 * @param $lang string with language code or false
	 * @return mixed
	 */
	public function getFieldStaticValueI18n($fieldName, $lang)
	{
		if (!$this->hasFieldI18n($fieldName))
		{
			if (!isset($this->fields[$fieldName]['staticValues']))
			{
				return null;
			}
			return $this->fields[$fieldName]['staticValues'];
		}
		if (!isset($this->fields[$fieldName]['staticValues'][$lang]))
		{
			return null;
		}
		return $this->fields[$fieldName]['staticValues'][$lang];
	}

	/*
	 * @param $fieldName string
	 * @return boolean if field is constructed by the values of other fields
	 */
	public function isFieldConstructed($fieldName)
	{
		return (!empty($this->fields[$fieldName]['desc']['isConstructed']));
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
	 * @param $fieldName string
	 * @return boolean true if field is an Object
	 */
	public function isFieldObject($fieldName)
	{
		$type = $this->getFieldType($fieldName);
		if (!$type)
		{
			// TODO: Exception: no type provided for field
			return false;
		}
		return $type->isObject();
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
	 * @return array
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
}
