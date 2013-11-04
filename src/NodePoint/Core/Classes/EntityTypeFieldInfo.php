<?php

namespace NodePoint\Core\Classes;

use NodePoint\Core\Library\TypeInterface;
use NodePoint\Core\Library\EntityTypeFieldInfoInterface;

class EntityTypeFieldInfo implements EntityTypeFieldInfoInterface {

	/*
	 * @var string
	 */
	protected $name;

	/*
	 * @var NodePoint\Core\Library\EntityTypeInterface
	 */
	protected $type;

	/*
	 * @var array
	 */
	protected $description;

	/*
	 * @var array
	 */
	protected $storageDesc;

	/*
	 * @var array
	 */
	protected $magicFuncs;

	/*
	 * Constructor
	 *
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 */
	public function __construct($name, $type)
	{
		$this->name = $name;
		$this->type = $type;
		$this->description = null;
		$this->storageDesc = null;
		$this->magicFuncs = null;
	}

	/*
	 * @param $name string with fieldName
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
	 * @param $type NodePoint\Core\Library\TypeInterface
	 */
	public function setType(TypeInterface $type)
	{
		$this->type = $type;
	}

	/*
	 * @return NodePoint\Core\Library\TypeInterface
	 */
	public function getType()
	{
		return $this->type;
	}

	/*
	 * @param $description array
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/*
	 * @return array
	 */
	public function getDescription()
	{
		if (!isset($this->description))
		{ 
			return null;
		}
		return $this->description;
	}

	/*
	 * @return boolean if field is array
	 */
	public function isArray()
	{
		return (!empty($this->description['isArray']));
	}

	/*
	 * @return boolean if field has multiple translations
	 */
	public function hasI18n()
	{
		return (!empty($this->description['i18n']));
	}

	/*
	 * @return boolean if field is accessable without instance
	 */
	public function isStatic()
	{
		return (!empty($this->description['static']));
	}

	/*
	 * @return boolean if field is readOnly
	 */
	public function isReadOnly()
	{
		if (isset($this->description['readOnly']))
		{
			return $this->description['readOnly'];
		}
		if (!empty($this->description['isStatic']))
		{
			return true;
		}
		if (!empty($this->description['isConstructed']))
		{
			return true;
		}
		return false;
	}

	/*
	 * @return boolean if field is constructed by the values of other fields
	 */
	public function isConstructed()
	{
		return (!empty($this->description['constructed']));
	}

	/*
	 * @param $fieldName string
	 * @return boolean if field is accessable by find operations
	 */
	public function isSearchable()
	{
		return (!empty($this->description['searchable']));
	}

	/*
	 * Base field names are used for constructed
	 * fields and for fields which have dynamic options
	 *
	 * @return mixed - string or array of string with fieldNames
	 */
	public function getBaseField()
	{
		if (!isset($this->description['baseField']))
		{
			return false;
		}
		return $this->description['baseField'];
	}

	/*
	 * Calculate capitalized version of string
	 *
	 * @param string
	 * @return string
	 */
	static protected function capitalizeString($string)
	{
		return strtoupper(substr($string, 0, 1)) . substr($string, 1);
	}

	/*
	 * Retrieve or calculate fields plural name
	 * based on the fieldName
	 *
	 * @return string
	 */
	public function getPluralName()
	{
		if (!isset($this->description['plural']))
		{
			if ($this->isArray())
			{
				return $fieldName;
			}
			else
			{
				$fieldNameLen = strlen();
				if ($fieldNameLen - 1 == strrchr('s'))
				{
					return $fieldName . 'es';
				}
				elseif ($fieldNameLen - 1 == strrchr('y'))
				{
					return substr(0, $fieldNameLen-1) . 'ies';
				}
				else
				{
					return $fieldName . 's';
				}
			}
		}
		return $this->description['plural'];
	}

	/*
	 * Retrieve or calculate fields plural capitalized name
	 * based on the fieldName
	 *
	 * @return string
	 */
	public function getPluralCapitalizedName()
	{
		if (!isset($this->description['pluralCapitalize']))
		{
			$pluralName = $this->getPluralName();
			return $this->capitalizeString($pluralName);
		}
		return $this->description['pluralCapitalize'];
	}

	/*
	 * Retrieve or calculate fields singular name
	 * based on the fieldName
	 *
	 * @return string
	 */
	public function getSingularName()
	{
		if (!isset($this->description['singular']))
		{
			if (!$this->isArray())
			{
				return $this->name;
			}
			else
			{
				$fieldNameLen = strlen();
				if ($fieldNameLen - 2 == strrpos('es'))
				{
					return substr(0, $fieldNameLen-2);
				}
				elseif ($fieldNameLen - 3 == strrchr('ies'))
				{
					return substr(0, $fieldNameLen-3) . 'y';
				}
				else
				{
					return substr(0, $fieldNameLen-1);
				}
			}
		}
		return $this->description['singular'];
	}

	/*
	 * Retrieve or calculate fields singular capitalized name
	 * based on the fieldName
	 *
	 * @return string
	 */
	public function getSingularCapitalizedName()
	{
		if (!isset($this->description['singularCapitalize']))
		{
			$singularName = $this->getSingularName();
			return $this->capitalizeString($singularName);
		}
		return $this->description['singularCapitalize'];
	}

	/*
	 * Base field names are used for constructed
	 * fields and for fields which have dynamic options
	 *
	 * @return mixed - string or array of string with field names
	 */
	public function getBaseFieldName()
	{
		if (!isset($this->description['baseField']))
		{
			return false;
		}
		return $this->description['baseField'];
	}

	/*
	 * @return boolean
	 */
	public function hasOptions()
	{
		return (!empty($this->description['hasOptions']));
	}

	/*
	 * @return mixed array or false
	 */
	public function getOptions()
	{
		if (!isset($this->description['options']))
		{
			return false;
		}
		return $this->description['options'];
	}

	/*
	 * @param array
	 */
	public function setStorageDesc($storageDesc)
	{
		$this->storageDesc = $storageDesc;
	}

	/*
	 * @return array
	 */
	public function getStorageDesc()
	{
		if (!isset($this->storageDesc))
		{ 
			return null;
		}
		return $this->storageDesc;
	}

	/*
	 * @return int - Int, Float, Text, Entity
	 */
	public function getStorageType()
	{
		if (!isset($this->storageDesc['type']))
		{ 
			return $this->type->getStorageType();
		}
		return $this->storageDesc['type'];
	}

	/*
	 * @param $callType string  
	 * @param $funcName string  
	 *			set, get, validate, 
	 *			cnt, setitem, getitem,
	 * @return string
	 */
	public function setMagicCallName($callType, $funcName)
	{
		if (!isset($this->magicFuncs))
		{
			$this->magicFuncs = array();
		}
		$this->magicFuncs[$callType] = $funcName;
	}

	/*
	 * @param $callType string  
	 *			set, get, validate, 
	 *			cnt, setitem, getitem,
	 * @return string
	 */
	public function getMagicCallName($callType)
	{
		if (!isset($this->magicFuncs[$callType]))
		{
			return null;
		}
		return $this->magicFuncs[$callType];
	}
}
