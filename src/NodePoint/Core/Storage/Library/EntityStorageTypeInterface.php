<?php

namespace NodePoint\Core\Storage\Library;

interface EntityStorageTypeInterface {

	/*
	 * @param $value mixed
	 * @return mixed
	 */
	public function valueToStorageColumn($value);

	/*
	 * @param $column mixed
	 * @return mixed
	 */
	public function valueFromStorageColumn($column);
}
