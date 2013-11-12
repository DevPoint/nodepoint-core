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
	 * @param $typeName string with entity type name
	 * @param $alias string
	 * @param $lang mixed string or array of string
	 * @param $mapFieldNames array indexed by fieldName
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function findByAlias($typeName, $alias, $lang=null, $mapFieldNames=null);

	/*
	 * @param $entity NodePoint\Core\Library\EntityInterface
	 */
	public function save(EntityInterface $entity);
}
