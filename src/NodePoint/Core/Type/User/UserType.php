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
		$parentType = $typeFactory->getType('NodePointCore/Node');
		parent::__construct('NodePointCore/User', $typeFactory, $parentType);
		$this->className = "\\NodePoint\\Core\\Type\\User\\User";
	}
}
