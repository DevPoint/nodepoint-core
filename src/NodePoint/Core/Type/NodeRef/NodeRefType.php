<?php

namespace NodePoint\Core\Type\NodeRef;

use NodePoint\Core\Classes\BaseEntityRefType;

class NodeRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('NodePointCore/NodeRef', $referenceTypeName);
	}
}