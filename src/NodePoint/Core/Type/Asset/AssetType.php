<?php

namespace NodePoint\Core\Type\Asset;

use NodePoint\Core\Classes\BaseNodeType;

class AssetType extends BaseNodeType {

	/*
	 * Constructor
	 *
	 * @param $parentType NodePoint\Core\Library\EntityTypeInterface
	 * @param $description array
	 */
	public function __construct()
	{
		parent::__construct('NodePointCore/Asset');
	}
}
