<?php

namespace NodePoint\Core\Type\EntityRef;

use NodePoint\Core\Classes\BaseEntityRefType;

class EntityRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('NodePointCore/EntityRef', $referenceTypeName);
	}
}