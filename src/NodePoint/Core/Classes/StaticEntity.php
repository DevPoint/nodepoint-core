<?php

namespace NodePoint\Core\Classes;

use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityTypeInterface;
use NodePoint\Core\Storage\Library\EntityStorageProxyInterface;

class StaticEntity extends AbstractEntity {

	/*
	 * @var array of NodePoint\Core\Library\EntityFieldInterface indexed by fieldName
	 */
	protected $fields;

	/*
	 * Constructor
	 *
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $fields array pf NodePoint\Core\Library\EntityFieldInterface
	 */
	public function __construct(EntityTypeInterface $type, $fields=array())
	{
		// basic construction
		parent::__construct($type);

		// add parent entity fields
		$parentType = $type->getParentType();
		if (null !== $parentType)
		{
			// ATTN: static entity of parent types
			// must already be completely initialized, 
			// in order to derive values correctly
			$staticEntity = $parentType->getStaticEntity();
			if (null !== $staticEntity)
			{
				$this->_addFieldsToCache($staticEntity->_fields());
			}
		}

		// add entity fields
		$this->fields = $fields;
		$this->_addFieldsToCache($fields);
	}

	/*
	 * @return array of NodePoint\Core\Library\EntityFieldInterface
	 */
	final public function _fields()
	{
		return $this->fields;
	}

	/*
	 * @param $name string callName
	 * @param $args array
	 * @return mixed field value or this
	 */
	public function __call($name, $args)
	{
		// get magic field call info
		$magicFieldCallInfo = $this->type->getMagicFieldStaticCallInfo($name);
		if (null !== $magicFieldCallInfo)
		{
			return $this->{$magicFieldCallInfo->functionCall}($magicFieldCallInfo->field, $args);
		}
		
		// TODO: Exception: unknown call
		// .
		// .

		return null;
	}
}