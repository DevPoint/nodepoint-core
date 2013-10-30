<?php

namespace NodePoint\Core\Type\Tag;

use NodePoint\Core\Classes\BaseEntityType;
use NodePoint\Core\Library\EntityTypeInterface;

class TagType extends BaseEntityType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 */
	public function __construct($typeFactory, $hasI18n=true)
	{
		parent::__construct('NodePointCore/Tag', $typeFactory, null);
	}
}

