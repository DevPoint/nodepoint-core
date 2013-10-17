<?php

namespace TinyCms\NodeProvider\Storage\Library;

interface EntityStorageProxyInterface {

	/*
	 * @return TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface
	 */
	public function getRepository();

	/*
	 * @return TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function getEntity();

	/*
	 * @param $fieldName string
	 */
	public function addUpdateField($fieldName);
}
