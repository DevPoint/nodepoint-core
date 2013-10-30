<?php

namespace NodePoint\Core\Storage\PDO\Library;

use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Storage\Library\EntityRepositoryInterface as BaseInterface;

interface EntityRepositoryInterface extends BaseInterface {

	/*
	 * @return string
	 */
	public function getEntityTableName();
	
}
