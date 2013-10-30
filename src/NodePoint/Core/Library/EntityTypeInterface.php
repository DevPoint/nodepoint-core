<?php

namespace NodePoint\Core\Library;

interface EntityTypeInterface extends TypeInterface {

	/*
	 * @return NodePoint\Core\Library\EntityTypeInterface
	 */
	public function getParentType();

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function setStaticEntity($entity);

	/*
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function getStaticEntity();

	/*
	 * @return array of string with fieldNames
	 */
	public function getFieldNames();
	
	/*
	 * @return string with fieldName
	 */
	public function getFieldNameByAlias($fieldNameAlias);

	/*
	 * @param $fieldName string
	 * @param $tType NodePoint\Core\Library\TypeInterface
	 */
	public function setFieldType($fieldName, TypeInterface $type);

	/*
	 * @param $fieldName string
	 * @return NodePoint\Core\Library\TypeInterface
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
	 * Base field names are used for constructed
	 * fields and for fields which have dynamic options
	 *
	 * @param $fieldName string
	 * @return mixed - string or array of string with field names
	 */
	public function getFieldBaseField($fieldName);

	/*
	 * Retrieve or calculate fields plural name
	 * based on the fieldName
	 *
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldPluralName($fieldName);

	/*
	 * Retrieve or calculate fields plural capitalized name
	 * based on the fieldName
	 *
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldPluralCapitalizedName($fieldName);

	/*
	 * Retrieve or calculate fields singular name
	 * based on the fieldName
	 *
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldSingularName($fieldName);

	/*
	 * Retrieve or calculate fields singular capitalized name
	 * based on the fieldName
	 *
	 * @param $fieldName string
	 * @return string
	 */
	public function getFieldSingularCapitalizedName($fieldName);

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
	 * @return boolean if field is accessable by find operations
	 */
	public function isFieldSearchable($fieldName);

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
	 * @param $callType string  
	 *			set, get, cnt, setitem, getitem,
	 *			lang, validate
	 * @return string
	 */
	public function getFieldMagicCallName($fieldName, $callType);

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
	 * @return int - Int, Float, Text, Entity
	 */
	public function getFieldStorageType($fieldName);

	/*
	 * @param $callName string
	 * @param $magicFieldCallInfo NodePoint\Core\Library\MagicFieldCallInfo
	 */
	public function setMagicFieldCallInfo($callName, MagicFieldCallInfo $magicFieldCallInfo);

	/*
	 * @param $callName string
	 * @return NodePoint\Core\Library\MagicFieldCallInfo
	 */
	public function getMagicFieldCallInfo($callName);

}