<?php

namespace TinyCms\NodeProvider\Type\Node;

use TinyCms\NodeProvider\Classes\BaseNodeType;

class NodeType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('TinyCmsNodeProvider/Node', $parentType, $description);
	}
}

