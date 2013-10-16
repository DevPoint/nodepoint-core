<?php

namespace TinyCms\NodeProvider\Type\NodeRef;

use TinyCms\NodeProvider\Classes\BaseEntityRefType;

class NodeRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsCore/NodeRef', $referenceTypeName);
	}
}