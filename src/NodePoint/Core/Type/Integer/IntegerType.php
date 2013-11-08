<?php

namespace NodePoint\Core\Type\Integer;

use NodePoint\Core\Classes\BaseType;

class IntegerType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'Core/Integer';
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
			if (isset($rules['minValue']) && $value < $rules['minValue'])
			{
				$errors[] = 'minValue';
			}
			if (isset($rules['maxValue']) && $value > $rules['maxValue'])
			{
				$errors[] = 'maxValue';
			}
		}
		return (!empty($errors)) ? $errors : true;
	}

	/*
	 * @param $value mixed
	 * @return mixed string or int
	 */
	public function searchKeyFromValue($value)
	{
		return intval($value);
	}

	/*
	 * @param $fieldName string
	 * @return int - Int, Float, Text
	 */
	public function getStorageType()
	{
		return self::STORAGE_INT;
	}
}