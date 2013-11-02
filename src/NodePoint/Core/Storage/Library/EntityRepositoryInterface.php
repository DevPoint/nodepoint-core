<?php

namespace NodePoint\Core\Storage\Library;

use NodePoint\Core\Library\EntityInterface;

interface EntityRepositoryInterface {

	/*
	 * @return NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager();
	
	/*
	 * @param $entityId string
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function find($entityId);

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function save(EntityInterface $entity);

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 * @param $fieldName string
	 * @param $lang mixed string or array of string
	 * @return boolean
	 */
	public function loadField(EntityInterface $entity, $fieldName, $lang=null);
}
