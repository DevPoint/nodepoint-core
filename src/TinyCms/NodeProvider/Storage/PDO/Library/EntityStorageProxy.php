<?php

namespace TinyCms\NodeProvider\Storage\PDO\Library;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Library\EntityTypeInterface;
use TinyCms\NodeProvider\Storage\Library\EntityManagerInterface;
use TinyCms\NodeProvider\Storage\Library\EntityStorageProxyInterface;

class EntityStorageProxy implements EntityStorageProxyInterface {

	/*
	 * @var TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 */
	protected $em;

	/*
	 * @var TinyCms\NodeProvider\Library\EntityInterface
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
	 * @return TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager()
	{
		return $this->em;
	}

	/*
	 * @return TinyCms\NodeProvider\Library\EntityInterface
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
	public function addUpdateField($fieldName)
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
					$this->addUpdateField($fieldName);
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
}
