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
	 * @return boolean true if entity has been updated
	 */
	public function _hasUpdate();

	/*
	 * Reset any update flags
	 */
	public function _resetUpdate();

	/*
	 * @return array of string
	 */
	public function _getUpdateFieldNames();

	/*
	 * @param $repository TinyCms\NodeProvider\Library\EntityRepositoryInterface
	 */
	public function _setRepository(EntityRepositoryInterface $repository);

	/*
	 * @return TinyCms\NodeProvider\Library\EntityRepositoryInterface
	 */
	public function _getRepository();
}