<?php

namespace NodePoint\Core\Type\Position2d;

use NodePoint\Core\Classes\BaseType;

class Position2dType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'Core/Position2d';
		$this->className = "\\NodePoint\\Core\\Type\\Position2d\\Position2d";
	}

	/*
	 * @return boolean true for object types
	 */
	public function isObject()
	{
		return true;
	}

	/*
	 * @param $object object
	 * @return array
	 */
	public function objectToArray($object)
	{
		$result = array('x'=>$object->x, 'y'=>$object->y);
		return $result;
	}

	/*
	 * @param $value array
	 * @return object
	 */
	public function objectFromArray(&$arrValue)
	{
		$objectClassName = $this->getClassName();
		$object = new $objectClassName();
		$object->x = $arrValue['x'];
		$object->y = $arrValue['y'];
		return $object;
	}
}