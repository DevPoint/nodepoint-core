<?php

namespace NodePoint\Core\Library;

class TypeFactory {

	/*
	 * @var array of NodePoint\Core\Library\TypeInterface
	 */
	protected $types;

	/*
	 * Constructor
	 */
	public __construct()
	{
		$this->types = array();
	}

	/*
	 * @param $typeName string
	 * @param $type NodePoint\Core\Library\TypeInterface
	 */
	public function registerType($typeName, $type)
	{
		$this->types[$typeName] = $type;
	}

	/*
	 * @param $typeName string
	 * @return NodePoint\Core\Library\TypeInterface
	 */
	public function getType($typeName)
	{
		if (!isset($this->types[$typeName]))
		{
			return null;
		}
		return $this->types[$typeName];
	}
}

