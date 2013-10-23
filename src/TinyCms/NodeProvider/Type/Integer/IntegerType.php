<?php

namespace TinyCms\NodeProvider\Type\Integer;

use TinyCms\NodeProvider\Classes\BaseType;

class IntegerType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'TinyCmsCore/Integer';
	}

	/*
	 * @param $fieldName string
	 * @return int - Int, Float, Text
	 */
	public function getStorageType()
	{
		return self::STORAGE_INT;
	}
}