<?php

namespace NodePoint\Core\Storage\PDO\Library;

class ColumnInfo {

	public $paramType;

	public $nullValue;

	public function __construct($paramType, $nullValue)
	{
		$this->paramType = $paramType;
		$this->nullValue = $nullValue;
	}

}
