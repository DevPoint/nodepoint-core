<?php

namespace TinyCms\NodeProvider\Type\FolderMultiRef;

use TinyCms\NodeProvider\Classes\BaseEntityMultiRefType;

class FolderMultiRefType extends BaseEntityMultiRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsCore/FolderMultiRef', $referenceTypeName);
	}
}