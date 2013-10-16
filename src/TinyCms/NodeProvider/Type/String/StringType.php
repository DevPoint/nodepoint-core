<?php

namespace TinyCms\NodeProvider\Type\String;

use TinyCms\NodeProvider\Classes\BaseType;

class StringType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'TinyCmsCore/String';
	}
}