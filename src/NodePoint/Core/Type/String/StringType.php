<?php

namespace NodePoint\Core\Type\String;

use NodePoint\Core\Classes\BaseType;

class StringType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'NodePointCore/String';
	}

	/*
	 * @param $value mixed
	 * @param $rules array indexed by rule type
	 * @return mixed boolean true or array with errors
	 */
	public function validate(&$value, &$rules=null)
	{
		$errors = array();
		$length = mb_strlen($value, 'UTF-8');
		if (isset($rules['minLength']) && $length < $rules['minLength'])
		{
			$errors[] = 'minLength';
		}
		if (isset($rules['maxLength']) && $length > $rules['maxLength'])
		{
			$errors[] = 'maxLength';
		}
		return (!empty($errors)) ? $errors : true;
	}

	/*
	 * @param $value mixed
	 * @return mixed string or int
	 */
	public function searchKeyFromValue($value)
	{
		return mb_strtolower($value, 'UTF-8');
	}
}