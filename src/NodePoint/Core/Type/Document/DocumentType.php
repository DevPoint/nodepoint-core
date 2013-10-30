<?php

namespace NodePoint\Core\Type\Document;

use NodePoint\Core\Classes\BaseNodeType;

class DocumentType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct()
	{
		parent::__construct('NodePointCore/Document');

		// configure field name aliase
		$this->fieldNameAliases['_id'] = 'id';
		$this->fieldNameAliases['_alias'] = 'alias';
		$this->fieldNameAliases['_parent'] = 'parent';
		$this->fieldNameAliases['_parentName'] = 'parentName';
	}
}
