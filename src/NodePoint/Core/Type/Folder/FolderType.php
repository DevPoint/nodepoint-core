<?php

namespace NodePoint\Core\Type\Folder;

use NodePoint\Core\Classes\BaseNodeType;
use NodePoint\Core\Library\EntityTypeInterface;

class FolderType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 */
	public function __construct($typeFactory, $hasI18n=true)
	{
		$parentType = $typeFactory->getType('NodePointCore/Node');
		parent::__construct('NodePointCore/Folder', $typeFactory, $parentType);
	}
}

