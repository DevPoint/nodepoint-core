<?php

namespace NodePoint\Core\Type\Node;

use NodePoint\Core\Classes\BaseNodeType;
use NodePoint\Core\Library\TypeFactoryInterface;

class NodeType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\TypeFactoryInterface
	 */
	public function __construct(TypeFactoryInterface $typeFactory, $hasI18n=true)
	{
		// call parent constructor
		parent::__construct(
			'NodePointCore/Node', "\\NodePoint\\Core\\Type\\Node\\Node",
			$typeFactory, null);

		// get primitive types
		$integerType = $typeFactory->getType('NodePointCore/Integer');
		$stringType = $typeFactory->getType('NodePointCore/String');

		// add standard fields
		$this->setFieldInfo('id', $integerType, array('alias'=>'_id'));
		$this->setFieldInfo('parent', $this, array('alias'=>'_parent'));
		$this->setFieldInfo('parentField', $stringType, array('alias'=>'_parentField'));
	}
}

