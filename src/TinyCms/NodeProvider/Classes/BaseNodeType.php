<?php

namespace TinyCms\NodeProvider\Classes;

abstract class BaseNodeType extends BaseEntityType {

	/*
	 * Constructor
	 *
	 * @param $typeName string
	 */
	protected function __construct($typeName, $parentType, $description)
	{
		parent::__construct($typeName, $parentType, $description);
	}


}

