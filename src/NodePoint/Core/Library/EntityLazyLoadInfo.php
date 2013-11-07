<?php

namespace NodePoint\Core\Library;

class EntityLazyLoadInfo {

	/*
	 * @var string
	 */
	public $entityId;	

	/*
	 * @var string
	 */
	public $typeName;

	/*
	 * @param $entityId string
	 * @param $typeName string
	 */
	public function __construct($entityId, $typeName)
	{
		$this->entityId = $entityId;
		$this->typeName = $typeName;
	}
}
