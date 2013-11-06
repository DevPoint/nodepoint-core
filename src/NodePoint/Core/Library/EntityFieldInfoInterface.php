<?php

namespace NodePoint\Core\Library;

interface EntityFieldInfoInterface {

	/*
	 * @return string with fieldName
	 */
	public function getName();

	/*
	 * @return NodePoint\Core\Library\TypeInterface
	 */
	public function getType();

	/*
	 * Function lock will be called from
	 * the finalize function
	 */
	public function lock();

	/*
	 * @return boolean if field info can't be changed
	 */
	public function locked();

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
	 * @return boolean if field is readOnly
	 */
	public function isReadOnly();

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
	 * Alias names always starting with
	 * underscore (_id,_parent,_alias)
	 *
	 * @return string with alias name
	 */
	public function getNameAlias();
	
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
	 * @return array
	 */
	public function getStorageDesc();

	/*
	 * @param $callType string  
	 *			set, get, validate, getId
	 *			cnt, setitem, getitem,
	 * @return string
	 */
	public function getMagicCallName($callType);
}