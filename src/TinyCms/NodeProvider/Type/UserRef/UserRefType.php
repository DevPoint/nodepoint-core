<?php

namespace TinyCms\NodeProvider\Type\UserRef;

use TinyCms\NodeProvider\Classes\BaseEntityRefType;

class UserRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsCore/UserRef', $referenceTypeName);
	}
}