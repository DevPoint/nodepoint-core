<?php

namespace TinyCms\NodeProvider\Storage\PDO\Serialize;

use TinyCms\NodeProvider\Storage\PDO\Classes\BaseSerializer;

class StringSerializer extends BaseSerializer {
	
	/*	
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('TinyCmsCore/StringSerializer');
	}

};
