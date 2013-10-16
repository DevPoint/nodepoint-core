<?php

namespace TinyCms\NodeProvider\Type\AssetMultiRef;

use TinyCms\NodeProvider\Classes\BaseEntityMultiRefType;

class AssetMultiRefType extends BaseEntityMultiRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsCore/AssetMultiRef', $referenceTypeName);
	}
}