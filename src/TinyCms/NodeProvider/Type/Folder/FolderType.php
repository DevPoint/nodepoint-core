<?php

namespace TinyCms\NodeProvider\Type\Folder;

use TinyCms\NodeProvider\Classes\BaseNodeType;

class FolderType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('TinyCmsCore/Folder', $parentType, $description);
	}
}

