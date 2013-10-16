<?php

namespace TinyCms\NodeProvider\Type\FolderRef;

use TinyCms\NodeProvider\Classes\BaseNodeRefType;

class FolderRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct($referenceTypeName)
	{
		parent::__construct('TinyCmsNodeProvider/FolderRef', $referenceTypeName);
	}
}