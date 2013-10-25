<?php

namespace NodePoint\Core\Storage\PDO\Library;

class PDOColumnInfo {

	public $paramType;

	public $nullValue;

	public function __construct($paramType, $nullValue)
	{
		$this->paramType = $paramType;
		$this->nullValue = $nullValue;
	}

}
