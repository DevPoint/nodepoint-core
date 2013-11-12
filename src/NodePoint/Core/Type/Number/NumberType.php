<?php

namespace NodePoint\Core\Type\Number;

use NodePoint\Core\Classes\BaseType;

class NumberType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'Core/Number';
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
	 * @return int - Int, Text
	 */
	public function getSearchKeyType()
	{
		return self::STORAGE_INT;
	}

	/*
	 * @param $value mixed
	 * @return mixed string or int
	 */
	public function searchKeyFromValue($value)
	{
		$maxInteger = 2147483647;
		if (0 > bccomp($value, -$maxInteger))
		{
			$value = -$maxInteger;
		}
		elseif (0 < bccomp($value, $maxInteger))
		{
			$value = $maxInteger;
		}
		return intval($value);
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