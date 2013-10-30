<?php

namespace NodePoint\Core\Type\Image;

use NodePoint\Core\Classes\BaseNodeType;
use NodePoint\Core\Library\EntityTypeInterface;

class ImageType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 */
	public function __construct($typeFactory, $hasI18n=true)
	{
		$parentType = $typeFactory->getType('NodePointCore/Node');
		parent::__construct('NodePointCore/Image', $typeFactory, $parentType);
	}
}
