<?php

namespace TinyCms\NodeProvider\Storage\Library;

interface SerializerInterface {

	/*	
	 * @return string
	 */
	public function getTypeName();

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