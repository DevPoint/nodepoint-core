<?php

namespace NodePoint\Core\Storage\Library;

use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityFieldInterface;

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
	 * @param $field NodePoint\Core\Library\EntityFieldInterface
	 * @return boolean
	 */
	public function loadField(EntityInterface $entity, EntityFieldInterface $field);
}
