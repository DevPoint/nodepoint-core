<?php

namespace NodePoint\Core\Library;

use NodePoint\Core\Storage\Library\EntityStorageProxyInterface;

interface EntityInterface {

	/*
	 * @return NodePoint\Core\Library\EntityTypeInterface
	 */
	public function _type();

	/*
	 * @return NodePoint\Core\Library\EntityTypeInterface
	 */
	public function _fieldType($fieldName);

	/*
	 * @return array of NodePoint\Core\Library\EntityFieldInterface
	 */
	public function _fields();

	/*
	 * @param $repository NodePoint\Core\Storage\Library\EntityRepositoryInterface
	 */
	public function _setStorageProxy(EntityStorageProxyInterface $storageProxy);

	/*
	 * @return NodePoint\Core\Storage\Library\EntityRepositoryInterface
	 */
	public function _getStorageProxy();

	/*
	 * Perform lazy loading of a field
	 *
	 * @param $field NodePoint\Core\Library\EntityFieldInterface
	 * @return boolean
	 */
	public function _loadField($field);
}
