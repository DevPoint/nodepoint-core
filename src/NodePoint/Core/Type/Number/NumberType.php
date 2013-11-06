<?php

namespace NodePoint\Core\Type\Number;

use NodePoint\Core\Classes\BaseType;

class NumberType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'NodePointCore/Number';
	}

	/*
	 * @param $value mixed
	 * @param $rules array indexed by rule type
	 * @return mixed boolean true or array with errors
	 */
	public function validate(&$value, &$rules=null)
	{
		$errors = array();
		if (!is_numeric($value))
		{
			$errors[] = 'notNumeric';
		}
		else
		{
			if (isset($rules['minValue']) && 0 > bccomp($value, $rules['minValue']))
			{
				$errors[] = 'minValue';
			}
			if (isset($rules['maxValue']) && 0 < bccomp($value, $rules['maxValue']))
			{
				$errors[] = 'maxValue';
			}
		}
		return (!empty($errors)) ? $errors : true;
	}

	/*
	 * @param $fieldName string
	 * @return int - Int, Float, Text
	 */
	public function getStorageType()
	{
		return self::STORAGE_FLOAT;
	}
}