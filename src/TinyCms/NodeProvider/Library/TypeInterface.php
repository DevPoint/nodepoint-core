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