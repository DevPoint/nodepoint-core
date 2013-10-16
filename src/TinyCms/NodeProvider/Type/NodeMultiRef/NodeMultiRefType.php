<?php

namespace TinyCms\NodeProvider\Type\NodeMultiRef;

use TinyCms\NodeProvider\Classes\BaseEntityMultiRefType;

class NodeMultiRefType extends BaseEntityMultiRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsCore/NodeMultiRef', $referenceTypeName);
	}
}