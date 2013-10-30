<?php

namespace NodePoint\Core\Type\Tag;

use NodePoint\Core\Classes\BaseEntityType;
use NodePoint\Core\Library\TypeFactoryInterface;

class TagType extends BaseEntityType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\TypeFactoryInterface
	 */
	public function __construct(TypeFactoryInterface $typeFactory, $hasI18n=true)
	{
		parent::__construct('NodePointCore/Tag', $typeFactory, null);
	}
}

