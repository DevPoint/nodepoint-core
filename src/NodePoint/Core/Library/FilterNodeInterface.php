<?php

namespace NodePoint\Core\Library;

interface FilterNodeInterface {

	/*
	 * @param $type string
	 */
	public function setType($type);

	/*
	 * @return string
	 */
	public function getType();

	/*
	 * @param $type string
	 */
	public function setFilterObjectType($objectType);

	/*
	 * @return string
	 */
	public function setFilterObjectType();

	/*
	 * @param $node NodePoint\Core\Library\NodeInterface
	 */
	public function setFilterRootNode($node);

	/*
	 * @return NodePoint\Core\Library\NodeInterface
	 */
	public function getFilterRootNode();

	/*
	 * @param $fieldName string
	 * @param $constraints array
	 */
	public function setFieldConstraints($fieldName, $constraints);

	/*
	 * @param $fieldName string
	 * @return array
	 */
	public function getFieldConstraints($fieldName);

	/*
	 * @param $fieldName string
	 * @param $constraintType string
	 */
	public function removeFieldConstraintType($fieldName, $constraintType);

	/*
	 * @param $fieldName string
	 * @param $constraint 
	 */
	public function removeFieldConstraint($fieldName, $constraint);

	public function clearFieldConstraints();
}