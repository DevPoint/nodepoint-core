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
	 * @var string
	 */
	public $parentTypeName;

	/*
	 * @var boolean
	 */
	public $isEntity;

	/*
	 * @param $className string
	 * @param $isEntity boolean
	 * @param $parentTypeName string
	 */
	public function __construct($className, $isEntity=false, $parentTypeName=null)
	{
		$this->className = $className;
		$this->isEntity = $isEntity;
		$this->parentTypeName = $parentTypeName;
		$this->type = null;
	}
}
