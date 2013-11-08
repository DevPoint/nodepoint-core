<?php

namespace NodePoint\Core\Type\ImageRef;

use NodePoint\Core\Classes\BaseEntityRefType;

class ImageRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct()
	{
		parent::__construct('Core/ImageRef', 'Core/Image');
	}
}