<?php

namespace NodePoint\Core\Classes;

use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityFieldInterface;
use NodePoint\Core\Library\EntityTypeInterface;
use NodePoint\Core\Storage\Library\EntityStorageProxyInterface;

class BaseEntity extends AbstractEntity {

	/*
	 * @var array of NodePoint\Core\Library\EntityFieldInterface indexed by fieldName
	 */
	protected $fields;

	/*
	 * @var NodePoint\Core\Storage\Library\EntityStorageProxyInterface
	 */
	protected $storageProxy;

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
		$this->storageProxy = null;

		// add static entity fields
		$staticEntity = $type->getStaticEntity();
		if (null !== $staticEntity)
		{
			$this->_addFieldsToCache($staticEntity->_fields());
		}

		// add entity fields
		$this->fields = $fields;
		$this->_addFieldsToCache($fields);
	}

	/*
	 * @param array of NodePoint\Core\Library\EntityFieldInterface
	 */
	final public function _addFields($fields)
	{
		$this->fields = array_merge($this->fields, $fields);
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
	 * Perform lazy loading of a field
	 *
	 * @param $field NodePoint\Core\Library\EntityFieldInterface
	 * @return boolean
	 */
	public function _loadField(EntityFieldInterface $field)
	{
		$storageProxy = $this->_getStorageProxy();
		if (!$storageProxy)
		{
			return false;
		}
		return $storageProxy->loadField($field);
	}

	/*
	 * @param $repository NodePoint\Core\Storage\Library\EntityStorageProxyInterface
	 */
	public function _setStorageProxy(EntityStorageProxyInterface $storageProxy)
	{
		$this->storageProxy = $storageProxy;
	}

	/*
	 * @return NodePoint\Core\Storage\Library\EntityRepositoryInterface
	 */
	final public function _getStorageProxy()
	{
		return $this->storageProxy;
	}

	/*
	 * @param $name string callName
	 * @param $args array
	 * @return mixed field value or this
	 */
	public function __call($name, $args)
	{
		// get magic field call info
		$magicFieldCallInfo = $this->type->getMagicFieldCallInfo($name);
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