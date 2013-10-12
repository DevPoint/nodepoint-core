<?php

namespace TinyCms\NodeProvider\Library;

class MagicFieldCallInfo {

	/*
	 * @var string
	 */
	public $field;	

	/*
	 * @var string
	 */
	public $functionCall;

	/*
	 * @param $field string
	 * @param $functionCall string
	 * @param $options array
	 */
	public function __construct($field, $functionCall, $options=array())
	{
		$this->field = $field;
		$this->functionCall = $functionCall;
	}
}
