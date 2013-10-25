<?php

namespace NodePoint\Core\Type\ImageRef;

use NodePoint\Core\Classes\BaseEntityRefType;

class ImageRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('NodePointCore/ImageRef', $referenceTypeName);
	}
}