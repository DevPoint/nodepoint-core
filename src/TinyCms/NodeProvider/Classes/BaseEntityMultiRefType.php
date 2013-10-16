<?php

namespace TinyCms\NodeProvider\Classes;

abstract class BaseEntityMultiRefType extends BaseType {

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
	 * @return boolean true if value refering to an object
	 */
	public function isReference()
	{
		return true;
	}

	/*
	 * @return string
	 */
	public function getReferenceTypeName()
	{
		return $this->referenceTypeName;
	}
}