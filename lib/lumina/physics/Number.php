<?php

namespace lumina\physics;

class Number{
	
	private $number;
	
	public function __construct($number){
		$this->set($number);
	}
	
	public function set($number){
		$this->number = $number;
	}
	
	public function get(Number $numObj = null){
		if(!is_null($numObj)) $this->number = $this->calculate($numObj);
		return $this->number;
	}
	
	public function is(Number $numObj){
		return $this == $numObj;
	}
	
	abstract protected function calculate(Number $numObj);
	
}