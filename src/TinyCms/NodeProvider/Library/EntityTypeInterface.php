<?php

namespace TinyCms\NodeProvider\Library;

interface EntityTypeInterface extends TypeInterface {

	/*
	 * Constants for storage types
	 */
	const STORAGE_STR = 1;
	const STORAGE_INT = 2;
	const STORAGE_FLOAT = 3;

	/*
	 * @return boolean true if inheritance isn't possible
	 */
	public function isFinal();

	/*
	 * @return TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	public function getParentType();

	/*
	 * @return string language code
	 */
	public function getDefaultLanguage();
	
	/*
	 * @return string
	 */
	public function getStorageRepositoryClass();

	/*
	 * @return string with table name
	 */
	public function getStorageTable();

	/*
	 * @return mixed string or array of string with storage id fieldName(s)
	 */
	public function getStorageIdFieldName();

	/*
	 * @return array of string with fieldNames
	 */
	public function getFieldNames();
	
	/*
	 * @param $fieldName string
	 * @param $tType TinyCms\NodeProvider\Library\TypeInterface
	 */
	public function setFieldType($fieldName, TypeInterface $type);

	/*
	 * @param $fieldName string
	 * @return TinyCms\NodeProvider\Library\TypeInterface
	 */
	public function getFieldType($fieldName);

	/*
	 * @param $fieldName string
	 * @param $description array
	 */
	public function setFieldDescription($fieldName, $description);

	/*
	 * @param $fieldName string
	 * @return array
	 */
	public function getFieldDescription($fieldName);

	/*
	 * @param $fieldName string
	 * @return boolean if field is the one with primary id
	 */
	public function isFieldPrimaryId($fieldName);

	/*
	 * @param $fieldName string
	 * @return boolean
	 */
	public function isFieldArray($fieldName);
	
	/*
	 * @param $fieldName string
	 * @return boolean if field is readOnly
	 */
	public function isFieldReadOnly($fieldName);

	/*
	 * @param $fieldName string
	 * @return boolean if field has multiple translations
	 */
	public function hasFieldI18n($fieldName);

	/*
	 * @param $fieldName string
	 * @return boolean if field is accessable without instance
	 */
	public function isFieldStatic($fieldName);

	/*
	 * @param $fieldName string
	 * @param $value mixed
	 */
	public function setFieldStaticValue($fieldName, $value);

	/*
	 * @param $fieldName string
	 * @return mixed
	 */
	public function getFieldStaticValue($fieldName);

	/*
	 * @param $fieldName string
	 * @param $lang string with language code or null
	 * @param $value mixed
	 */
	public function setFieldStaticValueI18n($fieldName, $lang, $value);
	/*
	 * @param $fieldName string
	 * @param $lang string with language code or null
	 * @return mixed
	 */
	public function getFieldStaticValueI18n($fieldName, $lang);

	/*
	 * Base field names are used for constructed
	 * fields and for fields which have dynamic options
	 *
	 * @param $fieldName string
	 * @return mixed - string or array of string with field names
	 */
	public function getFieldBaseField($fieldName);

	/*
	 * @param $fieldName string
	 * @return boolean true if field is an Object
	 */
	public function isFieldObject($fieldName);
	
	/*
	 * @param $fieldName string
	 * @return boolean true if field is an Entity
	 */
	public function isFieldEntity($fieldName);
	
	/*
	 * @param $fieldName string
	 * @return boolean if field is constructed by the values of other fields
	 */
	public function isFieldConstructed($fieldName);

	/*
	 * @param $fieldName string
	 * @return boolean
	 */
	public function hasFieldOptions($fieldName);

	/*
	 * @param $fieldName string
	 * @return array
	 */
	public function getFieldOptions($fieldName);

	/*
	 * @param $callName string
	 * @param $magicFieldCallInfo TinyCms\NodeProvider\Library\MagicFieldCallInfo
	 */
	public function setMagicFieldCallInfo($callName, MagicFieldCallInfo $magicFieldCallInfo);

	/*
	 * @param $callName string
	 * @return TinyCms\NodeProvider\Library\MagicFieldCallInfo
	 */
	public function getMagicFieldCallInfo($callName);

}