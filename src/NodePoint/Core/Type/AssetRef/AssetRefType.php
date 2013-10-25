<?php

namespace NodePoint\Core\Type\AssetRef;

use NodePoint\Core\Classes\BaseEntityRefType;

class AssetRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('NodePointCore/AssetRef', $referenceTypeName);
	}
}