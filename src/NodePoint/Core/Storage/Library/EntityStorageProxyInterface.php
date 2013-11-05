<?php

namespace NodePoint\Core\Storage\Library;

use NodePoint\Core\Library\EntityFieldInterface;

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
	public function onUpdateField($fieldName);

	/*
	 * @param $field NodePoint\Core\Library\EntityFieldInterface
	 * @return boolean
	 */
	public function loadField(EntityFieldInterface $field);
}
