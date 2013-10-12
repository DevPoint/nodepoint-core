<?php

namespace TinyCms\NodeProvider\Type\User;

use TinyCms\NodeProvider\Classes\BaseNodeType;

class UserType extends BaseNodeType {

	/*
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('TinyCmsNodeProvider/User');
	}
}
