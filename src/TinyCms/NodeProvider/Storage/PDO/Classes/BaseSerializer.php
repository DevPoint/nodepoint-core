<?php

namespace TinyCms\NodeProvider\Storage\PDO\Classes;

use TinyCms\NodeProvider\Storage\Library\SerializerInterface;

class BaseSerializer implements SerializerInterface {
	
	/*	
	 * @var string
	 */
	public $typeName;

	/*	
	 * Constructor
	 *
	 * @param string
	 */
	public function __construct($typeName)
	{
		$this->typeName = $typeName;
	}

	/*	
	 * @return string
	 */
	final public function getTypeName()
	{
		return $this->typeName;
	}

	/*
	 * @param $value mixed
	 * @return string
	 */
	public function serialize($value)
	{
		return $value;
	}

	/*
	 * @param $serializedValue string
	 * @return mixed
	 */
	public function unserialize($serializedValue)
	{
		return $value;
	}
};