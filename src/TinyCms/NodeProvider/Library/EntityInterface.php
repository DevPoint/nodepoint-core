<?php

namespace TinyCms\NodeProvider\Library;

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
	 * @return boolean true if field is an object
	 */
	public function _isFieldObject($fieldName);
}