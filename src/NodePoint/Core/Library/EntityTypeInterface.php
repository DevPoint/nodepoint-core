<?php

namespace NodePoint\Core\Library;

interface EntityTypeInterface extends TypeInterface {

	/*
	 * @return NodePoint\Core\Library\EntityTypeInterface
	 */
	public function getParentType();

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
	 * @param $type NodePoint\Core\Library\TypeInterface
	 * @param $description array
	 * @param $storageDesc array
	 */
	public function setFieldInfo($fieldName, TypeInterface $type, $description=null, $storageDesc=null);

	/*
	 * @param $fieldName string
	 * @return NodePoint\Core\Library\EntityTypeFieldInfoInterface
	 */
	public function getFieldInfo($fieldName);

	/*
	 * @param $fieldName string
	 * @return NodePoint\Core\Library\TypeInterface
	 */
	public function getFieldType($fieldName);

	/*
	 * @param $fieldName string
	 * @param array
	 */
	public function setFieldDescription($fieldName, $description);

	/*
	 * @param $fieldName string
	 * @return array
	 */
	public function getFieldDescription($fieldName);

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