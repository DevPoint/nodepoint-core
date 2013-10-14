<?php

namespace TinyCms\NodeProvider\Type\User;

use TinyCms\NodeProvider\Classes\BaseNodeType;

class UserType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('TinyCmsNodeProvider/User', $parentType, $description);
	}
}
