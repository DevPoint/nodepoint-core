<?php

namespace TinyCms\NodeProvider\Type\Tag;

use TinyCms\NodeProvider\Classes\BaseEntityType;

class TagType extends BaseEntityType {

	/*
	 * Constructor
	 *
	 * @param $parentType TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('TinyCmsCore/Tag', $parentType, $description);
	}
}

