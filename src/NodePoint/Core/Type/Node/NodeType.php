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

		// configure field name aliase
		$this->fieldNameAliases['_id'] = 'id';
		$this->fieldNameAliases['_alias'] = 'alias';
		$this->fieldNameAliases['_parent'] = 'parent';
		$this->fieldNameAliases['_parentField'] = 'parentField';

		// get primitive types
		$integerType = $typeFactory->getType('NodePointCore/Integer');
		$aliasType = $typeFactory->getType('NodePointCore/Alias');
		$stringType = $typeFactory->getType('NodePointCore/String');

		// add standard fields
		$this->setFieldType('id', $integerType);
		$this->setFieldType('parent', $this);
		$this->setFieldType('parentField', $stringType);
		$this->setFieldType('alias', $aliasType);
		$this->setFieldDescription('alias', array('i18n'=>$hasI18n, 'searchable'=>true));
	}
}

