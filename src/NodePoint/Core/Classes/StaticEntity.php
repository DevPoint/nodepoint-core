<?php

namespace NodePoint\Core\Classes;

use NodePoint\Core\Library\EntityInterface;
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
	public function __construct($type, $fields=array())
	{
		// basic construction
		parent::__construct($type);

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