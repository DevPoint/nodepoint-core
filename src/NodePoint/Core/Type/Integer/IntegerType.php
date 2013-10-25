<?php

namespace NodePoint\Core\Type\Integer;

use NodePoint\Core\Classes\BaseType;

class IntegerType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'NodePointCore/Integer';
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