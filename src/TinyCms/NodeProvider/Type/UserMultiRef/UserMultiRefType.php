<?php

namespace TinyCms\NodeProvider\Type\UserMultiRef;

use TinyCms\NodeProvider\Classes\BaseEntityMultiRefType;

class UserMultiRefType extends BaseEntityMultiRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsNodeProvider/UserMultiRef', $referenceTypeName);
	}
}