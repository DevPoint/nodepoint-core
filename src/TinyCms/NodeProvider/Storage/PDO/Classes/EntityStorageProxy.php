<?php

namespace TinyCms\NodeProvider\Storage\PDO\Classes;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Library\EntityTypeInterface;
use TinyCms\NodeProvider\Storage\Library\EntityManagerInterface;
use TinyCms\NodeProvider\Storage\Library\EntityStorageProxyInterface;
use TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface;

class EntityStorageProxy implements EntityStorageProxyInterface {

	/*
	 * @var TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface
	 */
	protected $repository;

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
	public function __construct(EntityRepositoryInterface $repository, EntityInterface $entity)
	{
		$this->repository = $repository;
		$this->entity = $entity;
		$this->updateFieldNames = null;
	}

	/*
	 * @return TinyCms\NodeProvider\Storage\Library\EntityRepositoryInterface
	 */
	public function getRepository()
	{
		return $this->repository;
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
	 * Reset any update flags
	 */
	public function resetUpdate()
	{
		$this->updateFieldNames = null;
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
			$this->repository->getEntityManager()->update($this->entity);
		}
		if (empty($this->updateFieldNames[$fieldName]))
		{
			$this->updateFieldNames[$fieldName] = true;
		}
	}
}
