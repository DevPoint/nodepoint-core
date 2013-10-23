<?php

namespace TinyCms\NodeProvider\Library;

use TinyCms\NodeProvider\Storage\Library\EntityStorageProxyInterface;

interface EntityInterface {

	/*
	 * @return TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	public function _type();

	/*
	 * @return TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	public function _fieldType($fieldName);

	/*
	 * @return array of TinyCms\NodeProvider\Library\EntityFieldInterface
	 */
	public function _fields();

	/*
	 * @param $repository TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface
	 */
	public function _setStorageProxy(EntityStorageProxyInterface $storageProxy);

	/*
	 * @return TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface
	 */
	public function _getStorageProxy();
}