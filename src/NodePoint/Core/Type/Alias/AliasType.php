<?php

namespace NodePoint\Core\Type\Alias;

use NodePoint\Core\Classes\BaseType;

class AliasType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'Core/Alias';
	}

	/*
	 * @param $value mixed
	 * @return mixed string or int
	 */
	public function searchKeyFromValue($value)
	{
		return mb_strtolower($value, 'UTF-8');
	}
}