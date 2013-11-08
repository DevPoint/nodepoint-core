<?php

namespace NodePoint\Core\Type\FolderRef;

use NodePoint\Core\Classes\BaseNodeRefType;

class FolderRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 *
	 * @param $referenceTypeName string
	 */
	public function __construct()
	{
		parent::__construct('Core/FolderRef', 'Core/Folder');
	}
}