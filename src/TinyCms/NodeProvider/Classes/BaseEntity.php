<?php

namespace TinyCms\NodeProvider\Classes;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Library\EntityRepositoryInterface;

class BaseEntity implements EntityInterface {

	/*
	 * @var TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	protected $type;

	/*
	 * @var TinyCms\NodeProvider\Library\EntityRepositoryInterface
	 */
	protected $repository;

	/*
	 * @var array of values indexed by fieldName
	 */
	protected $fields;

	/*
	 * @var array of string with fieldNames
	 */
	protected $updateFieldNames;

	/*
	 * Constructor
	 */
	public function __construct($type, $fields=array())
	{
		$this->type = $type;
		$this->repository = null;
		$this->fields = $fields;
		$this->updateFieldNames = null;
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
	 * @return boolean true if entity has been updated
	 */
	final public function _hasUpdate()
	{
		return (!empty($this->updateFieldNames));
	}

	/*
	 * Reset any update flags
	 */
	final public function _resetUpdate()
	{
		$this->updateFieldNames = null;
	}	

	/*
	 * @return array of string
	 */
	final public function _getUpdateFieldNames()
	{
		return array_keys($this->updateFieldNames);
	}	

	/*
	 * @param $fieldName string
	 */
	protected function addUpdateField($fieldName)
	{
		if (isset($this->repository))
		{
			if (null == $this->updateFieldNames)
			{
				$this->updateFieldNames = array();
				$this->repository->getEntityManager()->update($this);
			}
			if (empty($this->updateFieldNames[$fieldName]))
			{
				$this->updateFieldNames[$fieldName] = true;
			}
		}
	}

	/*
	 * @param $repository TinyCms\NodeProvider\Library\EntityRepositoryInterface
	 */
	final public function _setRepository(EntityRepositoryInterface $repository)
	{
		$this->repository = $repository;
	}

	/*
	 * @return TinyCms\NodeProvider\Library\EntityRepositoryInterface
	 */
	final public function _getRepository()
	{
		return $this->repository;
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
		$this->fields[$fieldName] = $args[0];
		$this->addUpdateField($fieldName);
		return $this;
	}

	/*
	 * @param $fieldName string 
	 * @return mixed field value
	 */
	protected function _getMagicFieldCall($fieldName)
	{
		if (!isset($this->fields[$fieldName]))
		{
			return null;
		}
		return $this->fields[$fieldName];
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
		if (!isset($this->fields[$fieldName]))
		{
			$this->fields[$fieldName] = array();	
		}
		$this->fields[$fieldName][$args[0]] = $args[1];
		$this->addUpdateField($fieldName);
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
		if (!isset($this->fields[$fieldName][$lang]))
		{
			return null;
		}
		return $this->fields[$fieldName][$lang];
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