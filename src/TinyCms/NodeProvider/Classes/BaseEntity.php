<?php

namespace TinyCms\NodeProvider\Classes;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Storage\Library\EntityStorageProxyInterface;
use TinyCms\NodeProvider\Classes\EntityField;
use TinyCms\NodeProvider\Classes\EntityArrayField;

class BaseEntity implements EntityInterface {

	/*
	 * @var TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	protected $type;

	/*
	 * @var TinyCms\NodeProvider\Storage\Library\EntityStorageProxyInterface
	 */
	protected $storageProxy;

	/*
	 * @var array of TinyCms\NodeProvider\Library\EntityFieldInterface indexed by fieldName
	 */
	protected $fields;

	/*
	 * @var array of values indexed by fieldName
	 */
	protected $cachedFields;

	/*
	 * Constructor
	 */
	public function __construct($type, $fields=array())
	{
		// store parameters
		$this->type = $type;
		$this->storageProxy = null;
		$this->fields = $fields;

		// create cachedFields array		
		$this->cachedFields = array();
		foreach ($fields as $field)
		{
			$fieldName = $field->getName();
			if ($type->hasFieldI18n($fieldName))
			{
				if (!isset($this->cachedFields[$fieldName]))
				{
					$this->cachedFields[$fieldName] = array();	
				}
				$lang = $field->getLanguage();
				$this->cachedFields[$fieldName][$lang] = $field;
			}
			else
			{
				$this->cachedFields[$fieldName] = $field;
			}
		}
	}

	/*
	 * @return TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	final public function _type()
	{
		return $this->type;
	}

	/*
	 * @param $fieldName string
	 * @return TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	final public function _fieldType($fieldName)
	{
		return $this->type->getFieldType($fieldName);
	}

	/*
	 * @return array of TinyCms\NodeProvider\Library\EntityFieldInterface
	 */
	final public function _fields()
	{
		return $this->fields;
	}

	/*
	 * @param $repository TinyCms\NodeProvider\Storage\Library\EntityStorageProxyInterface
	 */
	public function _setStorageProxy(EntityStorageProxyInterface $storageProxy)
	{
		$this->storageProxy = $storageProxy;
	}

	/*
	 * @return TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface
	 */
	final public function _getStorageProxy()
	{
		return $this->storageProxy;
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>value)
	 * @return boolean
	 */
	protected function _validateMagicFieldCall($fieldName, &$args)
	{
		return true;
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>value)
	 * @return TinyCms\NodeProvider\Library\EntityInstance
	 */
	protected function _setMagicFieldCall($fieldName, &$args)
	{
		if (!isset($this->cachedFields[$fieldName]))
		{
			if ($this->type->isFieldArray($fieldName))
			{
				$field = new EntityArrayField($fieldName, null);
				$this->cachedFields[$fieldName] = $field;
				$this->fields[] = $field;
			}
			else
			{
				$field = new EntityField($fieldName, null);
				$this->cachedFields[$fieldName] = $field;
				$this->fields[] = $field;
			}
		}
		$this->cachedFields[$fieldName]->setValue($args[0]);
		if (null !== $this->storageProxy)
		{
			$this->storageProxy->addUpdateField($fieldName);
		}
		return $this;
	}

	/*
	 * @param $fieldName string 
	 * @return mixed field value
	 */
	protected function _getMagicFieldCall($fieldName)
	{
		if (!isset($this->cachedFields[$fieldName]))
		{
			return null;
		}
		return $this->cachedFields[$fieldName]->getValue();
	}

	/*
	 * @param $fieldName string 
	 * @return mixed field value
	 */
	protected function _getMagicFieldStaticCall($fieldName)
	{
		return $this->type->getFieldStaticValue($fieldName);
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>language, 1=>value)
	 * @return TinyCms\NodeProvider\Library\EntityInstance
	 */
	protected function _setMagicFieldCallI18n($fieldName, &$args)
	{
		if (!isset($this->cachedFields[$fieldName]))
		{
			$this->cachedFields[$fieldName] = array();	
		}
		$lang = $args[0];
		if (!isset($this->cachedFields[$fieldName][$lang]))
		{
			if ($this->type->isFieldArray($fieldName))
			{
				$field = new EntityArrayField($fieldName, $lang);
				$this->cachedFields[$fieldName][$lang] = $field;
				$this->fields[] = $field;
			}
			else
			{
				$field = new EntityField($fieldName, $lang);
				$this->cachedFields[$fieldName][$lang] = $field;
				$this->fields[] = $field;
			}
		}
		$this->cachedFields[$fieldName][$lang]->setValue($args[1]);
		if (null !== $this->storageProxy)
		{
			$this->storageProxy->addUpdateField($fieldName);
		}
		return $this;
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>language)
	 * @return mixed field value
	 */
	protected function _getMagicFieldCallI18n($fieldName, &$args)
	{
		$lang = $args[0];
		if (!isset($this->cachedFields[$fieldName][$lang]))
		{
			return null;
		}
		return $this->cachedFields[$fieldName][$lang]->getValue();
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>language)
	 * @return mixed field value
	 */
	protected function _getMagicFieldStaticCallI18n($fieldName, &$args)
	{
		return $this->type->getFieldStaticValueI18n($fieldName, $args[0]);
	}

	/*
	 * @param $name string callName
	 * @param $args array
	 * @return mixed field value or this
	 */
	public function __call($name, $args)
	{
		// get magic field call info
		$magicFieldCallInfo = $this->type->getMagicFieldCallInfo($name);
		if (null === $magicFieldCallInfo)
		{
			// TODO: Exception: unknown call
			return $this;
		}

		// executing function
		return $this->{$magicFieldCallInfo->functionCall}($magicFieldCallInfo->field, $args);
	}
}