<?php

namespace TinyCms\NodeProvider\Storage\Library;

use TinyCms\NodeProvider\Library\EntityInterface;

interface EntityRepositoryInterface {

	/*
	 * @return TinyCms\NodeProvider\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager();
	
	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function save(EntityInterface $entity);
}
