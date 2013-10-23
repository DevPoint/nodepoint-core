<?php

namespace TinyCms\NodeProvider\Classes;

class EntityArrayField extends BaseEntityArrayField {

	/*
	 * @param $value mixed
	 */
	public function setValue($value)
	{
		if (is_array($value) && !empty($value))
		{
			$this->items = array();
			foreach ($value as $itemValue)
			{
				$item = new EntityField(null, null);
				$item->setValue($value);
				$this->items[] = $item;
			}
		}
	}
}
