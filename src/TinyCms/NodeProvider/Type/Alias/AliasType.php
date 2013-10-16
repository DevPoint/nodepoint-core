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
}