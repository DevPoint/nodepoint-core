<?php

namespace NodePoint\Core\Type\Folder;

use NodePoint\Core\Classes\BaseNodeType;

class FolderType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('NodePointCore/Folder', $parentType, $description);
	}
}

