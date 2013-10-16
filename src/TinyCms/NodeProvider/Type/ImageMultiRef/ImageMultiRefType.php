<?php

namespace TinyCms\NodeProvider\Type\ImageMultiRef;

use TinyCms\NodeProvider\Classes\BaseEntityMultiRefType;

class ImageMultiRefType extends BaseEntityMultiRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsCore/ImageMultiRef', $referenceTypeName);
	}
}