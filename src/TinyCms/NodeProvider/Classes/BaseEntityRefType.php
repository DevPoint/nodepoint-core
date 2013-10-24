<?php

namespace TinyCms\NodeProvider\Classes;

abstract class BaseEntityRefType extends BaseType {

	/*
	 * @var string
	 */
	protected $referenceTypeName;


	/*
	 * Constructor
	 *
	 * @param $typeName string
	 * @param $referenceTypeName string
	 */
	protected function __construct($typeName, $referenceTypeName)
	{
		$this->typeName = $typeName;
		$this->referenceTypeName = $referenceTypeName;
	}

	/*
	 * @return boolean true for entity types
	 */
	final public function isEntity()
	{
		return true;
	}

	/*
	 * @return boolean true if value refering to an entity
	 */
	final public function isReference()
	{
		return true;
	}

	/*
	 * @return string
	 */
	final public function getReferenceTypeName()
	{
		return $this->referenceTypeName;
	}
}