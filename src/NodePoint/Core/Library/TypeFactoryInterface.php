<?php

namespace NodePoint\Core\Library;

interface TypeFactoryInterface {

	/*
	 * @param $typeName string
	 * @param $type NodePoint\Core\Library\TypeInterface
	 */
	public function registerTypeClass($typeName, $className);

	/*
	 * @param $typeName string
	 * @param $type NodePoint\Core\Library\TypeInterface
	 */
	public function registerType(TypeInterface $type);

	/*
	 * @param $typeName string
	 * @return NodePoint\Core\Library\TypeInterface
	 */
	public function getType($typeName);
}

