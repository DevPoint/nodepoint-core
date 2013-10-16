<?php

namespace TinyCms\NodeProvider\Type\AssetRef;

use TinyCms\NodeProvider\Classes\BaseEntityRefType;

class AssetRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsCore/AssetRef', $referenceTypeName);
	}
}