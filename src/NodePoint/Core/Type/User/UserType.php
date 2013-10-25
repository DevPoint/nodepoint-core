<?php

namespace NodePoint\Core\Type\User;

use NodePoint\Core\Classes\BaseNodeType;

class UserType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct($parentType=null, $description=array())
	{
		parent::__construct('NodePointCore/User', $parentType, $description);
	}
}
