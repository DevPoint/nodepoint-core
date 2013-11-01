<?php

namespace NodePoint\Core\Type\User;

use NodePoint\Core\Classes\BaseNodeType;
use NodePoint\Core\Library\TypeFactoryInterface;

class UserType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\TypeFactoryInterface
	 */
	public function __construct(TypeFactoryInterface $typeFactory)
	{
		// call parent constructor
		$parentType = $typeFactory->getType('NodePointCore/Node');
		parent::__construct(
			'NodePointCore/User', "\\NodePoint\\Core\\Type\\User\\User",
			$typeFactory, $parentType);
	}
}
