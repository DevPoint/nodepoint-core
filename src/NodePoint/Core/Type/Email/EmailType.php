<?php

namespace NodePoint\Core\Type\Email;

use NodePoint\Core\Classes\BaseType;

class EmailType extends BaseType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->typeName = 'NodePointCore/Email';
	}
}