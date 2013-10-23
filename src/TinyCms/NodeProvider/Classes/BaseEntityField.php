<?php

namespace TinyCms\NodeProvider\Classes;

class BaseEntityField extends AbstractEntityField {

	/*
	 * @var mixed
	 */
	protected $value;

	/*
	 * @param $name string with fieldName
	 * @param $lang string with language code
	 * @param $value mixed
	 */
	public function __construct($name, $lang)
	{
		parent::__construct($name, $lang);
		$this->value = null;
	}

	/*
	 * @param mixed
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	/*
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}
}