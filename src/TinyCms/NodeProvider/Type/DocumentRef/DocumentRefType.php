<?php

namespace TinyCms\NodeProvider\Type\DocumentRef;

use TinyCms\NodeProvider\Classes\BaseEntityRefType;

class DocumentRefType extends BaseEntityRefType {

	/*
	 * Constructor
	 * @param $referenceTypeName string
	 */
	public function __construct(EntityTypeInterface $referenceTypeName)
	{
		parent::__construct('TinyCmsCore/DocumentRef', $referenceTypeName);
	}
}