<?php

namespace TinyCms\NodeProvider\Value\EntityMultiRef;

use TinyCms\NodeProvider\Classes\BaseEntityMultiRefType;

class EntityMultiRefType extends BaseEntityMultiRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsNodeProvider/EntityMultiRef', $referenceTypeName);
	}
}