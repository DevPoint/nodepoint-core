<?php

namespace NodePoint\Core\Library;

interface TypeInterface {

	/*
	 * Constants for storage types
	 */
	const STORAGE_NONE = 	0;
	const STORAGE_TEXT = 	1;
	const STORAGE_INT = 	2;
	const STORAGE_FLOAT = 	3;
	const STORAGE_ENTITY = 	4;

	/*
	 * @return string
	 */
	public function getTypeName();

	/*
	 * @param $type string
	 * @return boolean true if type or any of the parent types matches
	 */
	public function isTypeName($typeName);

	/*
	 * @param $type string
	 * @return boolean true if type matches
	 */
	public function isTypeNameExact($typeName);
	
	/*
	 * @return string
	 */
	public function getClassName();

	/*
	 * @return boolean true for object types
	 */
	public function isObject();

	/*
	 * @return boolean true for entity types
	 */
	public function isEntity();

	/*
	 * @return boolean true for node types
	 */
	public function isNode();

	/*
	 * @return boolean true if type is refering to an entity
	 */
	public function isReference();

	/*
	 * @return string
	 */
	public function getReferenceTypeName();

	/*
	 * @param $value mixed
	 * @param $rules array indexed by rule type
	 * @return mixed boolean true or array with errors
	 */
	public function validate(&$value, &$rules=null);

	/*
	 * @param $object mixed
	 * @return mixed - array or input parameter type
	 */
	public function objectToArray($object);

	/*
	 * @param $value mixed
	 * @return mixed - object or input parameter type
	 */
	public function objectFromArray(&$value);

	/*
	 * @param $fieldName string
	 * @return int - None, Int, Text
	 */
	public function getSearchKeyType();

	/*
	 * @param $value mixed
	 * @return mixed string or int
	 */
	public function searchKeyFromValue($value);

	/*
	 * @param $fieldName string
	 * @return int - Int, Float, Text, Entity
	 */
	public function getStorageType();

	/*
	 * @param $object object
	 * @return string
	 */
	public function objectToStorage($object);

	/*
	 * @param $value string
	 * @return object
	 */
	public function objectFromStorage(&$storageValue);
	
	/*
	 * Calculate further values from the given properties
	 */
	public function finalize();

}