<?php

namespace NodePoint\Core\Type\Image;

use NodePoint\Core\Classes\BaseNodeType;

class ImageType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('NodePointCore/Image', $parentType, $description);
	}
}
