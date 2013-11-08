<?php

namespace NodePoint\Core\Type\EntityRef;

use NodePoint\Core\Classes\BaseEntityRefType;

class EntityRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct()
	{
		parent::__construct('Core/EntityRef', 'Core/Entity');
	}
}