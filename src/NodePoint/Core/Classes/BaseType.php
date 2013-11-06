<?php

namespace NodePoint\Core\Classes;

use NodePoint\Core\Library\TypeInterface;

abstract class BaseType implements TypeInterface {

	/*
	 * @var string
	 */
	protected $typeName;

	/*
	 * @var string
	 */
	protected $className;

	/*
	 * @param $typeName string
	 */
	public function setTypeName($typeName)
	{
		$this->typeName = $typeName;
	}

	/*
	 * @return string
	 */
	public function getTypeName()
	{
		return $this->typeName;
	}

	/*
	 * @param $typeName string
	 * @return boolean true if type or any of the parent types matches
	 */
	public function isTypeName($typeName)
	{
		return ($typeName === $this->getTypeName());
	}

	/*
	 * @param $typeName string
	 * @return boolean true if type matches
	 */
	public function isTypeNameExact($typeName)
	{
		return ($typeName === $this->getTypeName());
	}

	/*
	 * @param $className string
	 */
	final public function setClassName($className)
	{
		$this->className = $className;
	}

	/*
	 * @return string
	 */
	final public function getClassName()
	{
		return $this->className;
	}

	/*
	 * @return boolean true for object types
	 */
	public function isObject()
	{
		return (!empty($this->className));
	}

	/*
	 * @return boolean true for entity types
	 */
	public function isEntity()
	{
		return false;
	}

	/*
	 * @return boolean true for node types
	 */
	public function isNode()
	{
		return false;
	}

	/*
	 * @return boolean true if type is refering to an entity
	 */
	public function isReference()
	{
		return false;
	}

	/*
	 * @return string
	 */
	public function getReferenceTypeName()
	{
		return null;
	}

	/*
	 * @param $fieldName string
	 * @return int - Int, Float, Text, Entity
	 */
	public function getStorageType()
	{
		return self::STORAGE_TEXT;
	}

	/*
	 * @param $value mixed
	 * @param $rules array indexed by rule type
	 * @return mixed boolean true or array with errors
	 */
	public function validate(&$value, &$rules=null)
	{
		return true;
	}

	/*
	 * @param $value mixed
	 * @return mixed string or int
	 */
	public function searchKeyFromValue($value)
	{
		return null;
	}

	/*
	 * @param $object object
	 * @return array
	 */
	public function objectToArray($object)
	{
		return null;
	}

	/*
	 * @param $value array
	 * @return mixed - object or input parameter type
	 */
	public function objectFromArray(&$arrValue)
	{
		return null;
	}

	/*
	 * @param $object object
	 * @return string
	 */
	public function objectToSerialized($object)
	{
		$arrValue = $this->objectToArray($object);
		if (null === $arrValue)
		{
			return null;
		}
		return serialize($arrValue);
	}

	/*
	 * @param $value string
	 * @return object
	 */
	public function objectFromSerialized(&$serializedValue)
	{
		$arrValue = unserialize($serializedValue);
		if (null === $arrValue)
		{
			return null;
		}
		return $this->objectFromArray($arrValue);
	}

	/*
	 * Calculate further values from the given properties
	 */
	public function finalize()
	{
	}
}