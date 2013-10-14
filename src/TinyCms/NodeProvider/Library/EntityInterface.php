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
}