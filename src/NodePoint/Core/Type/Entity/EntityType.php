<?php

namespace NodePoint\Core\Type\Entity;

use NodePoint\Core\Classes\BaseEntityType;

class EntityType extends BaseEntityType {

	/*
	 * Constructor
	 *
	 * @param $typeName string
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct()
	{
		parent::__construct('NodePointCore/Entity');
	}
}

