<?php

namespace TinyCms\NodeProvider\Type\Position2d;

class Position2d {

	/*
	 * @var mixed int or float
	 */
	public $x;
	
	/*
	 * @var mixed int or float
	 */
	public $y;

	/*
	 * Constructor
	 */
	public function __construct()
	{
		$this->x = 0;
		$this->y = 0;
	}

	/*
	 * @param $x int or float
	 * @param $y int or float
	 */
	public function set($x, $y)
	{
		$this->x = $x;
		$this->y = $y;
	}
}