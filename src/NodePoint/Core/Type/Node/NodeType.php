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
	public function __construct()
	{
		// call parent constructor
		parent::__construct('NodePointCore/Node');

		// configure field name aliase
		$this->fieldNameAliases['_id'] = 'id';
		$this->fieldNameAliases['_alias'] = 'alias';
		$this->fieldNameAliases['_parent'] = 'parent';
		$this->fieldNameAliases['_parentName'] = 'parentName';
	}
}

