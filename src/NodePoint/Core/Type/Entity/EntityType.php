<?php

namespace NodePoint\Core\Type\Entity;

use NodePoint\Core\Classes\BaseEntityType;
use NodePoint\Core\Library\TypeFactoryInterface;

class EntityType extends BaseEntityType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\TypeFactoryInterface
	 */
	public function __construct(TypeFactoryInterface $typeFactory, $hasI18n=true)
	{
		// call parent constructor
		parent::__construct(
			'NodePointCore/Entity', "\\NodePoint\\Core\\Type\\Entity\\Entity",
			$typeFactory, null);

		// configure field name aliase
		$this->fieldNameAliases['_id'] = 'id';
		$this->fieldNameAliases['_parent'] = 'parent';
		$this->fieldNameAliases['_parentField'] = 'parentField';

		// add standard fields
		$this->setFieldType('id', $typeFactory->getType('NodePointCore/Integer'));
		$this->setFieldType('parentField', $typeFactory->getType('NodePointCore/String'));
		$this->setFieldType('parent', $this);
	}
}

