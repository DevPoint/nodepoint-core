<?php

namespace TinyCms\NodeProvider\Storage\PDO;

use TinyCms\NodeProvider\Library\EntityInterface;
use TinyCms\NodeProvider\Library\EntityTypeInterface;
use TinyCms\NodeProvider\Library\EntityManagerInterface;
use TinyCms\NodeProvider\Library\EntityRepositoryInterface;

class EntityRepository implements EntityRepositoryInterface {

	/*
	 * @var PDO
	 */
	protected $conn;

	/*
	 * @var TinyCms\NodeProvider\Library\EntityManagerInterface
	 */
	protected $em;

	/*
	 * @var TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	protected $type;

	/*
	 * @param $conn (\PDO
	 * @param $em TinyCms\NodeProvider\Library\EntityManagerInterface
	 * @param $type TinyCms\NodeProvider\Library\EntityTypeInterface
	 */
	public function __construct(\PDO $conn, EntityManagerInterface $em, EntityTypeInterface $type)
	{
		$this->conn = $conn;
		$this->em = $em;
		$this->type = $type;
	}

	/*
	 * @return TinyCms\NodeProvider\Library\EntityManagerInterface
	 */
	public function getEntityManager()
	{
		return $this->em;
	}

	/*
	 * @param $entity TinyCms\NodeProvider\Library\EntityInterface
	 */
	public function save(EntityInterface $entity)
	{
	}
}