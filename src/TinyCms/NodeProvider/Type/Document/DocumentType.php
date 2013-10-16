<?php

namespace TinyCms\NodeProvider\Type\Document;

use TinyCms\NodeProvider\Classes\BaseNodeType;

class DocumentType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('TinyCmsCore/Document', $parentType, $description);
	}
}
