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
		// call parent constructor
		parent::__construct(
			'NodePointCore/Tag', "\\NodePoint\\Core\\Type\\Tag\\Tag",
			$typeFactory, null);
	}
}

