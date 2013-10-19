<?php

namespace TinyCms\NodeProvider\Storage\Library;

interface EntityTypeStorageInterface {

	/*
	 * @return TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	public function getType();

	/*
	 * @param $fieldName string
	 */
	public function addUpdateField($fieldName);
}
