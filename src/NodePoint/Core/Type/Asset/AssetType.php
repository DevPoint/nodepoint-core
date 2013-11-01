<?php

namespace NodePoint\Core\Type\Asset;

use NodePoint\Core\Library\TypeFactoryInterface;
use NodePoint\Core\Classes\BaseNodeType;

class AssetType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\TypeFactoryInterface
	 */
	public function __construct(TypeFactoryInterface $typeFactory, $hasI18n=true)
	{
		// call parent constructor
		$parentType = $typeFactory->getType('NodePointCore/Node');
		parent::__construct(
			'NodePointCore/Asset', "\\NodePoint\\Core\\Type\\Asset\\Asset", 
			$typeFactory, $parentType);
	}
}
