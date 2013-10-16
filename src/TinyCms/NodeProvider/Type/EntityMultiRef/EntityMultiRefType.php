<?php

namespace TinyCms\NodeProvider\Type\EntityMultiRef;

use TinyCms\NodeProvider\Classes\BaseEntityMultiRefType;

class EntityMultiRefType extends BaseEntityMultiRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsCore/EntityMultiRef', $referenceTypeName);
	}
}