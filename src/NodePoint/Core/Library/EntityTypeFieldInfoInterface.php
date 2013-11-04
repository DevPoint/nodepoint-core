<?php

namespace NodePoint\Core\Library;

interface EntityTypeFieldInfoInterface {

	/*
	 * @param $name string with fieldName
	 */
	public function setName($name);

	/*
	 * @return string with fieldName
	 */
	public function getName();

	/*
	 * @param $type NodePoint\Core\Library\TypeInterface
	 */
	public function setType(TypeInterface $type);

	/*
	 * @return NodePoint\Core\Library\TypeInterface
	 */
	public function getType();

	/*
	 * @param $description array
	 */
	public function setDescription($description);

	/*
	 * @return array
	 */
	public function getDescription();

	/*
	 * @return boolean
	 */
	public function isArray();
	
	/*
	 * @return boolean if field has multiple translations
	 */
	public function hasI18n();

	/*
	 * @return boolean if field is accessable without instance
	 */
	public function isStatic();

	/*
	 * @return boolean if field is readOnly
	 */
	public function isReadOnly();

	/*
	 * @return boolean if field is constructed by the values of other fields
	 */
	public function isConstructed();

	/*
	 * @return boolean if field is accessable by find operations
	 */
	public function isSearchable();

	/*
	 * @return boolean
	 */
	public function hasOptions();

	/*
	 * @return array
	 */
	public function getOptions();

	/*
	 * Base field names are used for constructed
	 * fields and for fields which have dynamic options
	 *
	 * @return mixed - string or array of string with field names
	 */
	public function getBaseFieldName();

	/*
	 * Retrieve or calculate fields plural name
	 * based on the fieldName
	 *
	 * @return string
	 */
	public function getPluralName();

	/*
	 * Retrieve or calculate fields plural capitalized name
	 * based on the fieldName
	 *
	 * @return string
	 */
	public function getPluralCapitalizedName();

	/*
	 * Retrieve or calculate fields singular name
	 * based on the fieldName
	 *
	 * @return string
	 */
	public function getSingularName();

	/*
	 * Retrieve or calculate fields singular capitalized name
	 * based on the fieldName
	 *
	 * @return string
	 */
	public function getSingularCapitalizedName();

	/*
	 * @param array
	 */
	public function setStorageDesc($storageDesc);

	/*
	 * @return array
	 */
	public function getStorageDesc();

	/*
	 * @param $callType string  
	 * @param $funcName string  
	 *			set, get, validate, getId
	 *			cnt, setitem, getitem,
	 * @return string
	 */
	public function setMagicCallName($callType, $funcName);

	/*
	 * @param $callType string  
	 *			set, get, validate, getId
	 *			cnt, setitem, getitem,
	 * @return string
	 */
	public function getMagicCallName($callType);
}