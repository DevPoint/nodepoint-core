<?php

namespace TinyCms\NodeProvider\Library;

interface EntityRepositoryInterface {

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function save(EntityInterface $entity);
}
