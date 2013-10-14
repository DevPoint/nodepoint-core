<?php

namespace TinyCms\NodeProvider\Library;

interface TypeInterface {

	/*
	 * @param $type string
	 */
	public function setTypeName($typeName);

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
	 * @return boolean
	 */
	public function isEntity();

	/*
	 * @return boolean
	 */
	public function isNode();

	/*
	 * @return boolean
	 */
	public function isReference();

	/*
	 * @return string
	 */
	public function getReferenceTypeName();
}