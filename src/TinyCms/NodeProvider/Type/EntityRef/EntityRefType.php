<?php

namespace TinyCms\NodeProvider\Type\EntityRef;

use TinyCms\NodeProvider\Classes\BaseEntityRefType;

class EntityRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsCore/EntityRef', $referenceTypeName);
	}
}