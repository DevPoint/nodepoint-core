<?php

namespace TinyCms\NodeProvider\Storage\PDO\Classes;

use TinyCms\NodeProvider\Storage\Library\SerializerInterface;

class BaseSerializer implements SerializerInterface {
	
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