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
		$parentType = $typeFactory->getType('NodePointCore/Node');
		parent::__construct('NodePointCore/Asset', $typeFactory, $parentType);
		$this->className = "\\NodePoint\\Core\\Type\\Asset\\Asset";
	}
}
