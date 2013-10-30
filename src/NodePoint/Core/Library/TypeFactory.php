<?php

namespace NodePoint\Core\Library;

use NodePoint\Core\Classes\TypeInfo;

class TypeFactory implements TypeFactoryInterface {

	/*
	 * @var array of NodePoint\Core\Classes\TypeIfno
	 */
	protected $types;

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->types = array();
	}

	/*
	 * @param $typeName string
	 * @param $type NodePoint\Core\Library\TypeInterface
	 */
	public function registerTypeClass($typeName, $className)
	{
		$typeInfo = new TypeInfo($className);
		$this->types[$typeName] = $typeInfo;
	}

	/*
	 * @param $typeName string
	 * @param $type NodePoint\Core\Library\TypeInterface
	 */
	public function registerType(TypeInterface $type)
	{
		$typeName = $type->getTypeName();
		$typeInfo = new TypeInfo(null);
		$typeInfo->type = $type;
		$this->types[$typeName] = $typeInfo;
	}

	/*
	 * @param $typeName string
	 * @return NodePoint\Core\Library\TypeInterface
	 */
	public function getType($typeName)
	{
		// unknown type
		if (!isset($this->types[$typeName]))
		{
			return null;
		}
		// instantiate type if needed
		$typeInfo = $this->types[$typeName];
		if (null === $typeInfo->type)
		{
			$className = $typeInfo->className;
			if (null !== $className)
			{
				$typeClass = $typeInfo->className;
				$typeInfo->type = new $typeClass();
			}
		}
		return $typeInfo->type;
	}
}

