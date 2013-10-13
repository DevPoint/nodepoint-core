<?php

namespace TinyCms\NodeProvider\Value\NodeMultiRef;

use TinyCms\NodeProvider\Classes\BaseNodeMultiRefType;

class NodeMultiRefType extends BaseEntityMultiRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsNodeProvider/NodeMultiRef', $referenceTypeName);
	}
}