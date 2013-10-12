<?php

namespace TinyCms\NodeProvider\Classes;

abstract class BaseNodeType extends BaseEntityType {

	/*
	 * Constructor
	 *
	 * @param $typeName string
	 */
	public function __construct($typeName)
	{
		parent::__construct($typeName);
	}


}

