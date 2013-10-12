<?php

namespace TinyCms\NodeProvider\Type\ImageRef;

use TinyCms\NodeProvider\Classes\BaseEntityRefType;

class ImageRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsNodeProvider/ImageRef', $referenceTypeName);
	}
}