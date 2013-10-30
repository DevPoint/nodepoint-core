<?php

namespace NodePoint\Core\Library;

use NodePoint\Core\Classes\TypeInfo;

class TypeFactory {

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
	 * @param $className string
	 */
	public function registerPrimitiveType($typeName, $className)
	{
		$typeInfo = new TypeInfo($className);
		$this->types[$typeName] = $typeInfo;
	}

	/*
	 * @param $typeName string
	 * @param $className string
	 * @param $parentTypeName string
	 */
	public function registerEntityType($typeName, $className, $parentTypeName)
	{
		$typeInfo = new TypeInfo($className, true, $parentTypeName);
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
			$typeClass = $typeInfo->className;
			if ($typeInfo->isEntity)
			{
				$parentType = null;
				if (null !== $typeInfo->parentTypeName)
				{
					$parentType = $this->getType($typeInfo->parentTypeName);
				}
				$typeInfo->type = new $typeClass($typeName, $parentType);
			}
			else
			{
				$typeInfo->type = new $typeClass($typeName);
			}

		}
		return $typeInfo->type;
	}
}

