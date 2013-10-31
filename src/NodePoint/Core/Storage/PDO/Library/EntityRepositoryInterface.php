<?php

namespace NodePoint\Core\Storage\PDO\Library;

use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityTypeInterface;
use NodePoint\Core\Storage\Library\EntityRepositoryInterface as BaseInterface;

interface EntityRepositoryInterface extends BaseInterface {

	/*
	 * @return string
	 */
	public function getEntityTableName();

	/*
	 * @param $typeName string
	 * @param $row entity table row
	 * @param $mapFieldNames array indexed by fieldName
	 * @param $lang mixed string or array of string
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function read($typeName, $row, $lang=null, $mapFieldNames=null);
	
}
