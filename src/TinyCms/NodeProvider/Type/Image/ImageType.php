<?php

namespace TinyCms\NodeProvider\Type\Image;

use TinyCms\NodeProvider\Classes\BaseNodeType;

class ImageType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('TinyCmsNodeProvider/Image', $parentType, $description);
	}
}
