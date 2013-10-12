<?php

namespace TinyCms\NodeProvider\Classes;

abstract class BaseNode extends BaseEntity {

	/*
	 * @return boolean true if object is a Node
	 */
	final public function isNode()
	{
		return true;
	}
}
