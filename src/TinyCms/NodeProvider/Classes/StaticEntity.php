<?php

namespace TinyCms\NodeProvider\Classes;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Storage\Library\EntityStorageProxyInterface;

class StaticEntity extends AbstractEntity {

	/*
	 * @var array of TinyCms\NodeProvider\Library\EntityFieldInterface indexed by fieldName
	 */
	protected $fields;

	/*
	 * Constructor
	 *
	 * @param $type TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @param $fields array pf TinyCms\NodeProvider\Library\EntityFieldInterface
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
	 * @return array of TinyCms\NodeProvider\Library\EntityFieldInterface
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