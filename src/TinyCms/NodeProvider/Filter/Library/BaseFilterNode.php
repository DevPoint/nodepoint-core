
<?php

namespace TinyCms\NodeProvider\Filter\Library;

use TinyCms\NodeProvider\Library\FilterNodeInterface

abstract class BaseFilterNode implements TinyCms\Library\FilterNodeInterface {

	/*
	 * @var string
	 */
	protected $type;

	/*
	 * @var string
	 */
	protected $filterObjectType;

	/*
	 * @var TinyCms\NodeProvider\Library\NodeInterface
	 */
	protected $rootNode;

	/*
	 * @var array
	 */
	protected $fieldConstraints;
	

	public function setType($type)
	{
		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setFilterObjectType($objectType)
	{
		$this->filterObjectType = $objectType;
	}

	public function getFilterObjectType()
	{
		return $this->filterObjectType;
	}

	public function setFilterRootNode($node)
	{
		$this->rootNode = $node;

	}

	public function getFilterRootNode()
	{
		return $this->rootNode;
	}

	public function setFieldConstraints($fieldName, $constraints)
	{
		$this->fieldConstraints[$fieldName] = $constraints;
	}

	public function getFieldConstraints($fieldName)
	{
		return $this->fieldConstraints[$fieldName];
	}

	public function removeFieldConstraintType($fieldName, $constraintType)
	{
		// TODO: implement
	}

	public function removeFieldConstraint($fieldName, $constraint)
	{
		// TODO: implement
	}

	public function clearFieldConstraints()
	{
		$this->fieldConstraints = array();
	}
}