<?php

namespace NodePoint\Core\Type\UserRef;

use NodePoint\Core\Classes\BaseEntityRefType;

class UserRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('NodePointCore/UserRef', $referenceTypeName);
	}
}