<?php

namespace TinyCms\NodeProvider\Type\Alias;

use TinyCms\NodeProvider\Classes\BaseType;

class AliasType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'TinyCmsCore/Alias';
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