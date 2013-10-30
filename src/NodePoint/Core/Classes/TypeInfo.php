<?php

namespace NodePoint\Core\Classes;

class TypeInfo {

	/*
	 * @var NodePoint\Core\Library\TypeInterface
	 */
	public $type;

	/*
	 * @var string
	 */
	public $className;

	/*
	 * @param $className string
	 * @param $isEntity boolean
	 * @param $parentTypeName string
	 */
	public function __construct($className)
	{
		$this->className = $className;
		$this->type = null;
	}
}
