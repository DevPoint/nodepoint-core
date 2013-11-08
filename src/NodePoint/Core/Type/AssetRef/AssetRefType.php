<?php

namespace NodePoint\Core\Type\AssetRef;

use NodePoint\Core\Classes\BaseEntityRefType;

class AssetRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct()
	{
		parent::__construct('Core/AssetRef', 'Core/Asset');
	}
}