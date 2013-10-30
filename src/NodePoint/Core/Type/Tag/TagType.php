<?php

namespace NodePoint\Core\Type\Tag;

use NodePoint\Core\Classes\BaseEntityType;

class TagType extends BaseEntityType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct()
	{
		parent::__construct('NodePointCore/Tag');
	}
}

