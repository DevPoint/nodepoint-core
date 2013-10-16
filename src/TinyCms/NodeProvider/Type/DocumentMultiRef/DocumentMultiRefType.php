<?php

namespace TinyCms\NodeProvider\Type\DocumentMultiRef;

use TinyCms\NodeProvider\Classes\BaseEntityMultiMultiRefType;

class DocumentMultiRefType extends BaseEntityMultiRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsCore/DocumentMultiRef', $referenceTypeName);
	}
}