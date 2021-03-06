<?php

namespace NodePoint\Core\Type\Folder;

use NodePoint\Core\Classes\BaseNodeType;
use NodePoint\Core\Library\TypeFactoryInterface;

class FolderType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\TypeFactoryInterface
	 */
	public function __construct(TypeFactoryInterface $typeFactory, $hasI18n=true)
	{
		// call parent constructor
		$parentType = $typeFactory->getType('Core/Node');
		parent::__construct(
			'Core/Folder', "\\NodePoint\\Core\\Type\\Folder\\Folder",
			$typeFactory, $parentType);
	}
}

