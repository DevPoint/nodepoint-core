<?php

namespace TinyCms\NodeProvider\Library;

interface EntityTypeInterface extends TypeInterface {

	/*
	 * Constants for storage types
	 */
	const STORAGE_TEXT = "TEXT";
	const STORAGE_INT = "INT";
	const STORAGE_FLOAT = "FLOAT";

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
	 * @return string with repository class name
	 */
	public function getStorageRepositoryClass();

	/*
	 * @return string with table name
	 */
	public function getStorageTable();

	/*
	 * @return array of string with fieldNames
	 */
	public function getFieldNames();
	
	/*
	 * @return mixd string or array of string with id fieldName(s)
	 */
	public function getIdFieldName();

	/*
	 * @return string with alias fieldName(s)
	 */
	public function getAliasFieldName();

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
	 * @param $fieldName string
	 * @param array
	 */
	public function setFieldStorageDesc($fieldName, $storageDesc);

	/*
	 * @param $fieldName string
	 * @return array
	 */
	public function getFieldStorageDesc($fieldName);

	/*
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldStorageTable($fieldName);

	/*
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldStorageColumn($fieldName);

	/*
	 * @param $fieldName string
	 * @return string - Int, Float, Text
	 */
	public function getFieldStorageType($fieldName);

	/*
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldStorageSql($fieldName);

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