<?php

namespace TinyCms\NodeProvider\Storage\PDO\Serialize;

use TinyCms\NodeProvider\Storage\PDO\Classes\BaseSerializer;

class ArraySerializer extends BaseSerializer {
	
	/*	
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('TinyCmsCore/ArraySerializer');
	}

	/*
	 * @param $value mixed
	 * @return string
	 */
	public function serialize($value)
	{
		return serialize($value);
	}

	/*
	 * @param $storageValue string
	 * @return mixed
	 */
	public function unserialize($serializedValue)
	{
		return unserialize($value);
	}
};
