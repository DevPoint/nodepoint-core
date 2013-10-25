<?php

namespace NodePoint\Core\Type\Node;

use NodePoint\Core\Classes\BaseNodeType;

class NodeType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('NodePointCore/Node', $parentType, $description);
	}
}

