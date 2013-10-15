<?php

namespace TinyCms\NodeProvider\Classes;

use TinyCms\NodeProvider\Library\TypeInterface;

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
	 * @return boolean
	 */
	public function isObject()
	{
		return (null !== $this->className);
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
	 * @return boolean
	 */
	public function isEntity()
	{
		return false;
	}

	/*
	 * @return boolean
	 */
	public function isNode()
	{
		return false;
	}

	/*
	 * @return boolean
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
		return false;
	}

	/*
	 * @param $object mixed
	 * @return mixed - array or input parameter type
	 */
	public function objectToValue($object, $options=null)
	{
		return $object;
	}

	/*
	 * @param $value mixed
	 * @return mixed - object or input parameter type
	 */
	public function objectFromValue(&$value)
	{
		return $value;
	}
}