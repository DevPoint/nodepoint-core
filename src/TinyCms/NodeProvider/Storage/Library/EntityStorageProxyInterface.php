<?php

namespace TinyCms\NodeProvider\Storage\Library;

interface EntityStorageProxyInterface {

	/*
	 * @return TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager();

	/*
	 * @return TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function getEntity();

	/*
	 * @param $fieldName string
	 */
	public function addUpdateField($fieldName);
}
