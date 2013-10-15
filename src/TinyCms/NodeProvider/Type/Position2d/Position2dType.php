<?php

namespace TinyCms\NodeProvider\Type\Position2d;

use TinyCms\NodeProvider\Classes\BaseType;

class Position2dType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'TinyCmsNodeProvider/Position2d';
		$this->className = 'TinyCms\NodeProvider\Type\Position2d\Position2d';
	}

	/*
	 * @return boolean
	 */
	public function isObject()
	{
		return true;
	}

	/*
	 * @param $object mixed
	 * @return mixed - array or input parameter type
	 */
	public function objectToValue($object, $options=null)
	{
		$result = array('x'=>$object->x, 'y'=>$object->y);
		return $result;
	}

	/*
	 * @param $value mixed
	 * @return mixed - object or input parameter type
	 */
	public function objectFromValue(&$value)
	{
		$object = new Position2d($value['x'], $value['y']);
		return $object;
	}
}