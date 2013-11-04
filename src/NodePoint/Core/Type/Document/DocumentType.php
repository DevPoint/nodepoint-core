<?php

namespace NodePoint\Core\Type\Document;

use NodePoint\Core\Classes\BaseNodeType;
use NodePoint\Core\Library\TypeFactoryInterface;

class DocumentType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\TypeFactoryInterface
	 */
	public function __construct(TypeFactoryInterface $typeFactory, $hasI18n=true)
	{
		// call parent constructor
		$parentType = $typeFactory->getType('NodePointCore/Node');
		parent::__construct(
			'NodePointCore/Document', "\\NodePoint\\Core\\Type\\Document\\Document", 
			$typeFactory, $parentType);
	}
}
