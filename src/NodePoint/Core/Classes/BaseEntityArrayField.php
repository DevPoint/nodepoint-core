<?php

namespace NodePoint\Core\Classes;

use NodePoint\Core\Storage\Library\EntityFieldInterface;

abstract class BaseEntityArrayField extends AbstractEntityField {

	/*
	 * @var array of NodePoint\Core\Storage\Library\EntityFieldInterface
	 */
	protected $items;

	/*
	 * @param $name string with fieldName
	 * @param $lang string with language code
	 */
	protected function __construct($name, $lang=null)
	{
		parent::__construct($name, $lang);
		$this->items = array();
	}

	/*
	 * @return mixed
	 */
	public function getValue()
	{
		$result = array();
		foreach ($items as $item)
		{
			$result[] = $item->getValue();
		}
		return $result;
	}

	/*
	 * @return boolean
	 */
	final public function isArray()
	{
		return true;
	}

	/*
	 * @return int
	 */
	final public function getArraySize()
	{
		return count($this->items);
	}

	/*
	 * @param $item NodePoint\Core\Library\EntityFieldInterface
	 */
	public function addArrayItem(EntityFieldInterface $item)
	{
		$this->items[] = $item;
	}

	/*
	 * @return boolean true for node types
	 */
	public function getArrayItems()
	{
		return $this->items;
	}

	/*
	 * @return NodePoint\Core\Library\EntityFieldInterface
	 */
	public function getArrayItem($index)
	{
		if (0 > $index || $index >= count($this->items))
		{
			return null;
		}
		return $this->items[$index];
	}
}
