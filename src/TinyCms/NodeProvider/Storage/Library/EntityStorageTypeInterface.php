<?php

namespace TinyCms\NodeProvider\Storage\Library;

interface EntityStorageTypeInterface {

	/*
	 * @param $value mixed
	 * @return mixed
	 */
	public function valueToStorageColumn($value);

	/*
	 * @param $column mixed
	 * @param $type TinyCms\NodeProvider\Library\EntityTypeInterface
	 * @return mixed
	 */
	public function valueFromStorageColumn($column, $type=null);
}
