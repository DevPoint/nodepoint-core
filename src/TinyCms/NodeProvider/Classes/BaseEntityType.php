<?php

namespace TinyCms\NodeProvider\Classes;

use TinyCms\NodeProvider\Library\MagicFieldCallInfo;
use TinyCms\NodeProvider\Library\TypeInterface;
use TinyCms\NodeProvider\Library\EntityTypeInterface;

abstract class BaseEntityType extends BaseType implements EntityTypeInterface {

	/*
	 * @var boolean true if inheritance isn't possible
	 */
	protected $finalState;

	/*
	 * @var TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	protected $parentType;

	/*
	 * @var boolean true if entity has I18n
	 */
	protected $i18nState;

	/*
	 * @var string language code
	 */
	protected $defaultLanguage;

	/*
	 * @var array indexed by fieldName
	 */
	protected $fields;

	/*
	 * @var array indexed by fieldName
	 */
	protected $staticFieldValues;

	/*
	 * @var array of TinyCms\NodeProvider\Library\MagicFieldCallInfo indexed by callName
	 */
	protected $magicFieldCallInfos;


	/*
	 * Constructor
	 *
	 * @param $typeName string
	 * @param $nodeState boolean
	 */
	public function __construct($typeName)
	{
		$this->typeName = $typeName;
		$this->finalState = false;
		$this->i18nState = false;
		$this->defaultLanguage = 'en';
		$this->fields = array();
		$this->staticFieldValues = array();
		$this->magicFieldCallInfos = array();
	}

	/*
	 * @return boolean
	 */
	final public function isEntity()
	{
		return true;
	}

	/*
	 * @return boolean true if entity has I18n
	 */
	final public function hasI18n()
	{
		return $this->i18nState;
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
	 * @param $type string
	 * @return boolean true if any of the parent types matches
	 */
	public function hasParentType($type)
	{
		$parentType = $this->getParentType();
		while (null !== $parentType)
		{
			if ($type == $parentType->getType())
			{
				return true;
			}
			$parentType = $parentType->getParentType();
		}
		return false;
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
		if (!isset($this->fields[$fieldName]) || (!isset($this->fields[$fieldName]['type'])))
		{ 
			return null;
		}
		return $this->fields[$fieldName]['type'];
	}

	/*
	 * @param $fieldName string
	 * @return boolean
	 */
	final protected function hasFieldDescription($fieldName)
	{
		return (isset($this->fields[$fieldName]) && isset($this->fields[$fieldName]['desc']));
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
		if (!$this->hasFieldDescription($fieldName))
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
		if (!$this->hasFieldDescription($fieldName))
		{ 
			return false;
		}
		return $this->fields[$fieldName]['desc']['isArray'];
	}
	
	/*
	 * @param $fieldName string
	 * @return boolean if field is readOnly
	 */
	public function isFieldReadOnly($fieldName)
	{
		if (!$this->hasFieldDescription($fieldName))
		{ 
			return false;
		}
		$readOnly = $this->fields[$fieldName]['desc']['readOnly'];
		if (isset($readOnly))
		{
			return $readOnly;
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
		if (!$this->hasFieldDescription($fieldName))
		{ 
			return false;
		}
		if (!isset($this->fields[$fieldName]['desc']['i18n']))
		{
			return false;
		}
		return $this->fields[$fieldName]['desc']['i18n'];
	}

	/*
	 * @param $fieldName string
	 * @return boolean if field is accessable without instance
	 */
	public function isFieldStatic($fieldName)
	{
		if (!$this->hasFieldDescription($fieldName))
		{ 
			return false;
		}
		if (!isset($this->fields[$fieldName]['desc']['isStatic']))
		{
			return false;
		}
		return $this->fields[$fieldName]['desc']['isStatic'];
	}

	/*
	 * @param $fieldName string
	 * @param $value mixed
	 */
	public function setFieldStaticValue($fieldName, $value)
	{
		if (!$this->isFieldStatic($fieldName))
		{
			return null;
		}
		if ($this->hasFieldI18n($fieldName))
		{
			// TODO: Exception: use i18n version of function to access
			return;
		}
		$this->staticFieldValues[$fieldName] = $value;
	}

	/*
	 * @param $fieldName string
	 * @return mixed
	 */
	public function getFieldStaticValue($fieldName)
	{
		if (!$this->isFieldStatic($fieldName))
		{
			return null;
		}
		if ($this->hasFieldI18n($fieldName))
		{
			// TODO: Exception use i18n version of function to access
			return null;
		}
		if (!isset($this->staticFieldValues[$fieldName]))
		{
			return null;
		}
		return $this->staticFieldValues[$fieldName];
	}

	/*
	 * @param $fieldName string
	 * @param $lang string with language code or null
	 * @param $value mixed
	 */
	public function setFieldStaticValueI18n($fieldName, $lang, $value)
	{
		if (!$this->isFieldStatic($fieldName))
		{
			return null;
		}
		if (!$this->hasFieldI18n($fieldName))
		{
			// TODO: Exception accessing no i18n field with language
			return;
		}
		if (!isset($this->staticFieldValues[$fieldName]))
		{
			$this->staticFieldValues[$fieldName] = array();
		}
		$this->staticFieldValues[$fieldName][$lang] = $value;
	}

	/*
	 * @param $fieldName string
	 * @param $lang string with language code or null
	 * @return mixed
	 */
	public function getFieldStaticValueI18n($fieldName, $lang)
	{
		if (!$this->isFieldStatic($fieldName))
		{
			return null;
		}
		if (!$this->hasFieldI18n($fieldName))
		{
			if (!isset($this->staticFieldValues[$fieldName]))
			{
				return null;
			}
			return $this->staticFieldValues[$fieldName];
		}
		if (!isset($this->staticFieldValues[$fieldName][$lang]))
		{
			return null;
		}
		return $this->staticFieldValues[$fieldName][$lang];
	}

	/*
	 * Base field names are used for constructed
	 * fields and for fields which have dynamic options
	 *
	 * @param $fieldName string
	 * @return mixed - string or array of string with field names
	 */
	public function getFieldBaseField($fieldName)
	{
		if (!$this->hasFieldDescription($fieldName))
		{ 
			return false;
		}
		if (!isset($this->fields[$fieldName]['desc']['baseField']))
		{
			return false;
		}
		return $this->fields[$fieldName]['desc']['baseField'];
	}

	/*
	 * @param $fieldName string
	 * @return boolean true if object is an Entity
	 */
	public function isFieldEntity($fieldName)
	{
		$type = $this->getFieldType($fieldName);
		if (!$type)
		{
			return false;
		}
		return $type->isEntity();
	}

	/*
	 * @param $fieldName string
	 * @return boolean if field is constructed by the values of other fields
	 */
	public function isFieldConstructed($fieldName)
	{
		if (!$this->hasFieldDescription($fieldName))
		{ 
			return false;
		}
		return $this->fields[$fieldName]['desc']['isConstructed'];
	}

	/*
	 * @param $fieldName string
	 * @return boolean
	 */
	public function hasFieldOptions($fieldName)
	{
		if (!$this->hasFieldDescription($fieldName))
		{ 
			return false;
		}
		return $this->fields[$fieldName]['desc']['hasOptions'];
	}

	/*
	 * @param $fieldName string
	 * @param $lang string with language code or null
	 * @return array of string indexed by field option value
	 */
	public function getFieldOptionNames($fieldName, $lang)
	{
		if (!$this->hasFieldOptions())
		{
			return false;
		}
		if (!isset($this->fields[$fieldName]['desc']['optionNames']))
		{
			return false;
		}
		if ($this->hasI18n())
		{
			return $this->fields[$fieldName]['desc']['optionNames'][$lang];
		}
		return $this->fields[$fieldName]['desc']['optionNames'];
	}

	/*
	 * @param $fieldName string
	 * @return boolean true if options are depending on the values of other fields
	 */
	public function hasFieldConstructedOptions($fieldName)
	{
		if (!$this->hasFieldOptions($fieldName))
		{ 
			return false;
		}
		return $this->fields[$fieldName]['desc']['hasConstructedOptions'];
	}

	/*
	 * @param $fieldName string
	 * @return array with associative array[option => name]
	 */
	public function getFieldStaticOptions($fieldName)
	{
		if (!$this->hasFieldOptions())
		{
			return false;
		}
		if (!isset($this->fields[$fieldName]['desc']['staticOptions']))
		{
			return false;
		}
		return $this->fields[$fieldName]['desc']['staticOptions'];
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
