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
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('NodePointCore/Document', $parentType, $description);
	}
}
