<?php

namespace NodePoint\Core\Storage\Library;

use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityFieldInterface;
use NodePoint\Core\Library\EntityTypeInterface;

interface EntityRepositoryInterface {

	/*
	 * @return NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager();
	
	/*
	 * @param $entityId string
	 * @param $lang mixed string or array of string
	 * @param $mapFieldNames array indexed by fieldName
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function find($entityId, $lang=null, $mapFieldNames=null);

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function save(EntityInterface $entity);

	/*
	 * @param $type NodePoint\Core\Library\EntityTypeInterface
	 * @param $field NodePoint\Core\Library\EntityFieldInterface
	 * @param $lang mixed string or array of string
	 * @return boolean
	 */
	public function loadField(EntityTypeInterface $type, EntityFieldInterface $field, $lang=null);
}
