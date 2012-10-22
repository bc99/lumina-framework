<?php

namespace lumina\events;

use lumina\core\Object;

class Event extends Object{
	/**
	 * @var String
	 */
	public $type;
	
	/**
	 * @var mixed
	 */
	public $root;
	
	/**
	 * @var mixed
	 */
	public $element;
	
	/**
	 * @var array
	 */
	public $addition;
	
	public function __construct($type, $root, $element, array $additional = array()){
		$this->type = (string) $type;
		$this->root = $root;
		$this->element = $element;
		$this->addition = $additional;
	}
}
