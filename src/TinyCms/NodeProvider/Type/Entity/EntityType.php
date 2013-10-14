<?php

namespace TinyCms\NodeProvider\Type\Entity;

use TinyCms\NodeProvider\Classes\BaseEntityType;

class EntityType extends BaseEntityType {

	/*
	 * Constructor
	 *
	 * @param $typeName string
	 * @param $parentType TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('TinyCmsNodeProvider/Entity', $parentType, $description);
	}
}

