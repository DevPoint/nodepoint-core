<?php

namespace NodePoint\Core\Storage\Library;

use NodePoint\Core\Library\EntityInterface;

interface EntityManagerInterface {

	/*
	 * @return NodePoint\Core\Library\TypeFactoryInterface
	 */
	public function getTypeFactory();

	/*
	 * @param $typeName string
	 * @param $repository NodePoint\Core\Storage\Library\EntityRepositoryInterface
	 */
	public function registerRepositoryClass($typeName, $className);
	
	/*
	 * @param $typeName string
	 * @return NodePoint\Core\Storage\Library\EntityRepositoryInterface
	 */
	public function getRepository($typeName);

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function persist(EntityInterface $entity);

	/*
	 * Writes all changes back to storage
	 */
	public function flush();

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function update(EntityInterface $entity);
	
	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function save(EntityInterface $entity);

	/*
	 * @param $typeName string
	 * @param $entityId string
	 * @param $lang mixed string or array of string
	 * @param $mapFieldNames array indexed by fieldName
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function find($typeName, $entityId, $lang=null, $mapFieldNames=null);
}