<?php

namespace NodePoint\Core\Classes;

use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Classes\EntityField;
use NodePoint\Core\Classes\EntityArrayField;
use NodePoint\Core\Storage\Library\EntityStorageProxyInterface;

class AbstractEntity implements EntityInterface {

	/*
	 * @var NodePoint\Core\Library\EntityTypeInterface
	 */
	protected $type;

	/*
	 * @var array of values indexed by fieldName
	 */
	protected $cachedFields;

	/*
	 * Constructor
	 */
	protected function __construct($type)
	{
		$this->type = $type;
		$this->cachedFields = array();
	}

	/*
	 * Primary called from the Constructor
	 *
	 * @param $fields array of NodePoint\Core\Library\EntityFieldInterface
	 */
	final protected function _addFieldsToCache(&$fields)
	{
		$entityType = $this->type;
		foreach ($fields as $field)
		{
			$fieldName = $field->getName();
			if ($entityType->hasFieldI18n($fieldName))
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
	 * @return NodePoint\Core\Library\EntityTypeInterface
	 */
	final public function _type()
	{
		return $this->type;
	}

	/*
	 * @param $fieldName string
	 * @return NodePoint\Core\Library\EntityTypeInterface
	 */
	final public function _fieldType($fieldName)
	{
		return $this->type->getFieldType($fieldName);
	}

	/*
	 * @param array of NodePoint\Core\Library\EntityFieldInterface
	 */
	public function _addFields($fields)
	{
	}

	/*
	 * @return array of NodePoint\Core\Library\EntityFieldInterface
	 */
	public function _fields()
	{
		return null;
	}

	/*
	 * @param $repository NodePoint\Core\Storage\Library\EntityStorageProxyInterface
	 */
	public function _setStorageProxy(EntityStorageProxyInterface $storageProxy)
	{
		// TODO: Exception: storage proxy not supported
	}

	/*
	 * @return NodePoint\Core\Storage\Library\EntityRepositoryInterface
	 */
	public function _getStorageProxy()
	{
		return null;
	}

	/*
	 * Perform lazy loading of a field
	 *
	 * @param $field NodePoint\Core\Library\EntityFieldInterface
	 * @return boolean
	 */
	public function _loadField($field)
	{
		return false;
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
	 * @return NodePoint\Core\Library\EntityInstance
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
		$storageProxy = $this->_getStorageProxy();
		if (null !== $storageProxy)
		{
			$storageProxy->onUpdateField($fieldName);
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
		$field = $this->cachedFields[$fieldName];
		if ($field->isLazyLoaded() && !$this->_loadField($field))
		{
			return null;
		}
		return $field->getValue();
	}

	/*
	 * @param $fieldName string 
	 * @return string with entity id
	 */
	protected function _getMagicFieldEntityIdCall($fieldName)
	{
		if (!isset($this->cachedFields[$fieldName]))
		{
			return null;
		}
		$field = $this->cachedFields[$fieldName];
		$value = $field->getValue();
		if ($field->isLazyLoaded())
		{
			return $value;
		}
		$entityType = $value->_type();
		$idFieldName = $entityType->getFieldNameByAlias('_id');
		$magicCallGetId = $entityType->getFieldMagicCallName($idFieldName, 'get');
		return $value->{$magicCallGetId}();
	}

	/*
	 * @param $fieldName string 
	 * @param $args array(0=>language, 1=>value)
	 * @return NodePoint\Core\Library\EntityInstance
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
		$storageProxy = $this->_getStorageProxy();
		if (null !== $storageProxy)
		{
			$storageProxy->onUpdateField($fieldName);
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
		$field = $this->cachedFields[$fieldName][$lang];
		if ($field->isLazyLoaded() && !$this->_loadField($field))
		{
			return null;
		}
		return $field->getValue();
	}
}
