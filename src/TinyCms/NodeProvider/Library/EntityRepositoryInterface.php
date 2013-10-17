<?php

namespace TinyCms\NodeProvider\Library;

interface EntityRepositoryInterface {

	/*
	 * @return TinyCms\NodeProvider\Library\EntityManagerInterface
	 */
	public function getEntityManager();
	
	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function save(EntityInterface $entity);
}
