<?php

namespace TinyCms\NodeProvider\Storage\Library;

interface SerializerInterface {
	
	/*
	 * @param $value mixed
	 * @return string
	 */
	public function serialize($value);

	/*
	 * @param $serializedValue string
	 * @return mixed
	 */
	public function unserialize($serializedValue);
};