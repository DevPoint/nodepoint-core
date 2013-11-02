<?php

namespace NodePoint\Core\Storage\PDO\Library;

use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityTypeInterface;
use NodePoint\Core\Storage\Library\EntityManagerInterface;
use NodePoint\Core\Storage\Library\EntityStorageProxyInterface;

class EntityStorageProxy implements EntityStorageProxyInterface {

	/*
	 * @var NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	protected $em;

	/*
	 * @var NodePoint\Core\Library\EntityInterface
	 */
	protected $entity;

	/*
	 * @var array of string with fieldNames
	 */
	protected $updateFieldNames;

	/*
	 * Constructor
	 */
	public function __construct(EntityManagerInterface $em, EntityInterface $entity)
	{
		$this->em = $em;
		$this->entity = $entity;
		$this->updateFieldNames = null;
	}

	/*
	 * @return NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager()
	{
		return $this->em;
	}

	/*
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function getEntity()
	{
		return $this->entity;
	}

	/*
	 * @return boolean true if entity has been updated
	 */
	public function hasUpdate()
	{
		return (!empty($this->updateFieldNames));
	}

	/*
	 * @return array of string
	 */
	public function getUpdateFieldNames()
	{
		return array_keys($this->updateFieldNames);
	}	

	/*
	 * @param $fieldName string
	 */
	public function onUpdateField($fieldName)
	{
		if (null == $this->updateFieldNames)
		{
			$this->updateFieldNames = array();
			$this->em->update($this->entity);
		}
		if (empty($this->updateFieldNames[$fieldName]))
		{
			$this->updateFieldNames[$fieldName] = true;
		}
	}

	/*
	 * @param $fieldName string
	 */
	public function hasUpdateField($fieldName)
	{
		return (!empty($this->updateFieldNames[$fieldName]));
	}

	/*
	 * All fields need an update
	 */
	public function updateAllFields()
	{
		$type = $this->entity->_type();
		$fieldNames = $type->getFieldNames();
		if (!empty($fieldNames))
		{
			foreach ($fieldNames as $fieldName)
			{
				if (!$type->isFieldReadOnly($fieldName))
				{
					$this->onUpdateField($fieldName);
				}
			}
		}
	}

	/*
	 * Reset any update flags
	 */
	public function resetUpdate()
	{
		$this->updateFieldNames = null;
	}	

	/*
	 * @param $fieldName string
	 * @param $lang mixed string or array of string
	 */
	public function loadField($fieldName, $lang=null)
	{
		$entity = $this->getEntity();
		$typeName = $entity->_type()->getTypeName();
		$repository = $this->em->getRepository($typeName);
		if (null === $repository)
		{
			return false;
		}
		return $repository->loadField($entiy, $fieldName, $lang);
	}
}
