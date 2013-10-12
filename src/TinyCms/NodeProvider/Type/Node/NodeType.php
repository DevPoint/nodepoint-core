<?php

namespace TinyCms\NodeProvider\Type\Node;

use TinyCms\NodeProvider\Classes\BaseNodeType;

class NodeType extends BaseNodeType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('TinyCmsNodeProvider/Node');
	}
}

