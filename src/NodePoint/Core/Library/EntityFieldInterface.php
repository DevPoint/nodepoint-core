<?php

namespace NodePoint\Core\Library;

interface EntityFieldInterface {

	/*
	 * @param $id string
	 */
	public function setId($id);

	/*
	 * @return string
	 */
	public function getId();

	/*
	 * @param $name string
	 */
	public function setName($name);
	
	/*
	 * @return string
	 */
	public function getName();

	/*
	 * @param $lang string with language code
	 */
	public function setLanguage($lang);

	/*
	 * @return string with language code
	 */
	public function getLanguage();

	/*
	 * @param $index int
	 */
	public function setSortIndex($index);

	/*
	 * @return int
	 */
	public function getSortIndex();

	/*
	 * @param $value mixed
	 */
	public function setValue($value);

	/*
	 * @return mixed
	 */
	public function getValue();

	/*
	 * @return boolean
	 */
	public function isArray();

	/*
	 * @return int
	 */
	public function getArraySize();

	/*
	 * @return array of NodePoint\Core\Library\EntityFieldInterface
	 */
	public function getArrayItems();

	/*
	 * @return NodePoint\Core\Library\EntityFieldInterface
	 */
	public function getArrayItem($index);

	/*
	 * @param $item NodePoint\Core\Library\EntityFieldInterface
	 */
	public function addArrayItem(EntityFieldInterface $item);
}