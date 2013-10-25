<?php

namespace NodePoint\Core\Storage\Library;

interface EntityStorageProxyInterface {

	/*
	 * @return NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager();

	/*
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function getEntity();

	/*
	 * @param $fieldName string
	 */
	public function addUpdateField($fieldName);
}
