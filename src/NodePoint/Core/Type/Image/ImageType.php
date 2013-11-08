<?php

namespace NodePoint\Core\Type\Image;

use NodePoint\Core\Classes\BaseNodeType;
use NodePoint\Core\Library\TypeFactoryInterface;

class ImageType extends BaseNodeType {

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
			'Core/Image', "\\NodePoint\\Core\\Type\\Image\\Image",
			$typeFactory, $parentType);
	}
}
