<?php

namespace NodePoint\Core\Type\DocumentRef;

use NodePoint\Core\Classes\BaseEntityRefType;

class DocumentRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct()
	{
		parent::__construct('NodePointCore/DocumentRef', 'NodePointCore/Document');
	}
}