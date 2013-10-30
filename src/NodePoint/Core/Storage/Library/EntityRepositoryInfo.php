<?php

namespace NodePoint\Core\Storage\Library;

class EntityRepositoryInfo {

	/*
	 * @var string
	 */
	public $className;

	/*
	 * @var NodePoint\Core\Storage\Library\EntityRepositoryInterface
	 */
	public $object;

	/*
	 * @param $className string
	 * @param $object NodePoint\Core\Storage\Library\EntityRepositoryInterface
	 */
	public function __construct($className, $object)
	{
		$this->className = $className;
		$this->object = $object;
	}
}
