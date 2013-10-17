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
}