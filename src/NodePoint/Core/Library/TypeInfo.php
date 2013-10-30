<?php

namespace NodePoint\Core\Library;

class TypeInfo {

	/*
	 * @var string
	 */
	public $parentTypeName;

	/*
	 * @var string
	 */
	public $className;

	/*
	 * @var NodePoint\Core\Library\TypeInterface
	 */
	public $type;

	/*
	 * @param $className string
	 * @param $type NodePoint\Core\Library\TypeInterface
	 */
	public function __construct($className, $parentTypeName)
	{
		$this->parentTypeName = $parentTypeName;
		$this->className = $className;
		$this->type = null;
	}
}
