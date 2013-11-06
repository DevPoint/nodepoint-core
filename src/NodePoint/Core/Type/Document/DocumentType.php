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
	
		// get primitive types
		$aliasType = $typeFactory->getType('NodePointCore/Alias');

		// add standard fields
		$this->setFieldInfo('alias', $aliasType, array('i18n'=>$hasI18n, 'searchable'=>true, 'alias'=>'_alias'));
	}
}
