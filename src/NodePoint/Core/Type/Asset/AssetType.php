<?php

namespace NodePoint\Core\Type\Asset;

use NodePoint\Core\Classes\BaseNodeType;
use NodePoint\Core\Library\EntityTypeInterface;

class AssetType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 */
	public function __construct($typeFactory, $hasI18n=true)
	{
		$parentType = $typeFactory->getType('NodePointCore/Node');
		parent::__construct('NodePointCore/Asset', $typeFactory, $parentType);
	}
}
