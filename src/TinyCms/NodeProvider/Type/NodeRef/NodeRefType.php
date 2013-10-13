<?php

namespace TinyCms\NodeProvider\Value\NodeRef;

use TinyCms\NodeProvider\Classes\BaseNodeRefType;

class NodeRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsNodeProvider/NodeRef', $referenceTypeName);
	}
}