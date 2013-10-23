<?php

namespace TinyCms\NodeProvider\Classes;

use TinyCms\NodeProvider\Library\EntityFieldInterface;

abstract class AbstractEntityField implements EntityFieldInterface {

	/*
	 * @var string with fieldName
	 */
	protected $name;

	/*
	 * @var mixed string with language code
	 */
	protected $lang;

	/*
	 * @var string
	 */
	protected $id;

	/*
	 * @param $name string with fieldName
	 * @param mixed string with language code
	 */
	protected function __construct($name, $lang)
	{
		$this->name = $name;
		$this->lang = $lang;
		$this->id = null;
	}

	/*
	 * @param string
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/*
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/*
	 * @param string with fieldName
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/*
	 * @return string with fieldName
	 */
	public function getName()
	{
		return $this->name;
	}

	/*
	 * @param mixed string with language code
	 */
	public function setLanguage($lang)
	{
		$this->lang = $lang;
	}

	/*
	 * @return mixed string with language code
	 */
	public function getLanguage()
	{
		return $this->lang;
	}

	/*
	 * @return boolean
	 */
	public function isArray()
	{
		return false;
	}

	/*
	 * @return int
	 */
	public function getArraySize()
	{
		return 0;
	}

	/*
	 * @param $item TinyCms\NodeProvider\Library\EntityFieldInterface
	 */
	public function addArrayItem(EntityFieldInterface $item)
	{
		// TODO: Exception: no array type
	}

	/*
	 * @return boolean true for node types
	 */
	public function getArrayItems()
	{
		return null;
	}

	/*
	 * @return boolean true for node types
	 */
	public function getArrayItem($index)
	{
		return null;
	}
}