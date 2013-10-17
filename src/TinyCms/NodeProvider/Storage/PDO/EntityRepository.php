<?php

namespace TinyCms\NodeProvider\Storage\PDO;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Library\EntityTypeInterface;
use TinyCms\NodeProvider\Library\EntityRepositoryInterface;

class EntityRepository implements EntityRepositoryInterface {

	/*
	 * @var PDO
	 */
	protected $conn;

	/*
	 * @var TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	protected $type;

	/*
	 * @param $type TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	public function __construct(\PDO $conn, EntityTypeInterface $type)
	{
		$this->conn = $conn;
		$this->type = $type;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function save(EntityInterface $entity)
	{
	}
}