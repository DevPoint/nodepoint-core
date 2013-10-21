<?php

namespace TinyCms\NodeProvider\Storage\Library;

use TinyCms\NodeProvider\Library\EntityInterface;

interface EntityManagerInterface {

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function persist(EntityInterface $entity);

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function save(EntityInterface $entity);
	
	/*
	 * Writes all changes back to storage
	 */
	public function flush();
}