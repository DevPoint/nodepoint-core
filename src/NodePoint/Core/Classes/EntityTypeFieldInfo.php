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
	 * @var NodePoint\Core\Library\TypeInterface
	 */
	protected $type;

	/*
	 * @var boolean
	 */
	protected $lockState;

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
	 * @param $type NodePoint\Core\Library\TypeInterface
	 */
	public function __construct($name, TypeInterface $type)
	{
		$this->name = $name;
		$this->type = $type;
		$this->description = null;
		$this->storageDesc = null;
		$this->magicFuncs = null;
		$this->lockState = false;
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
	 * Function lock will be called from
	 * the finalize function
	 */
	public function lock()
	{
		$this->lockState = true;
	}

	/*
	 * @return boolean if field info can't be changed
	 */
	public function locked()
	{
		return $this->lockState;
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
	 * @return boolean if field is readOnly
	 */
	public function isReadOnly()
	{
		if (isset($this->description['readOnly']))
		{
			return $this->description['readOnly'];
		}
		if (!empty($this->description['constructed']))
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
	public function getBaseFieldName()
	{
		if (!isset($this->description['baseField']))
		{
			return false;
		}
		return $this->description['baseField'];
	}

	/*
	 * Alias names always starting with
	 * underscore (_id,_parent,_alias)
	 *
	 * @return string with alias name
	 */
	public function getNameAlias()
	{
		if (!isset($this->description['alias']))
		{
			return false;
		}
		return $this->description['alias'];
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
	 * Calculate magic call names
	 */
	protected function _calculateMagicCallNames()
	{
		$this->magicFuncs = array();
		$singularName = $this->getSingularCapitalizedName();
		if ($this->isArray())		
		{
			$pluralName = $this->getPluralCapitalizedName();
			$this->magicFuncs['set'] = 'set' . $pluralName;
			$this->magicFuncs['get'] = 'get' . $pluralName;
			$this->magicFuncs['cnt'] = 'get' . $singularName . 'Count';
			$this->magicFuncs['setitem'] = 'set' . $singularName;
			$this->magicFuncs['getitem'] = 'get' . $singularName;
		}
		else
		{
			$this->magicFuncs['set'] = 'set' . $singularName;
			$this->magicFuncs['get'] = 'get' . $singularName;
			$this->magicFuncs['validate'] = 'validate' . $singularName;
			if ($this->type->isEntity())
			{
				$this->magicFuncs['getid'] = 'get' . $singularName . 'Id';
			}
		}
		//echo "Field:" . $this->name . '(' . implode(',' , $this->magicFuncs) . ")\n";
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
			if (null === $this->magicFuncs)
			{
				$this->_calculateMagicCallNames();
				if (isset($this->magicFuncs[$callType]))
				{
					return $this->magicFuncs[$callType];
				}
			}
			return null;
		}
		return $this->magicFuncs[$callType];
	}
}
