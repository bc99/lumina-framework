<?php

namespace lumina\physics;

abstract class Unit extends Number{
	
	private $type = 1;
	
	public function __construct($number, $type = null){
		$this->type = !is_null($type) ? $type : $this->getDefaultType();
		$this->set($number);
	}
	
	public function set($number){
		parent::setNumber($number*$this->type);
	}
	
	public function setType($type){
		$number = $this->get()/$this->type;
		$this->type = $type;
		$this->set($number);
	}
	
	public function getType(){
		return $this->type;
	}
	
	protected function calculate(Number $numObj){
		if(!$this->is($numObj)) throw new UnitException("unit is incorrect!");
		$number = $this->get()/$this->type;
		$this->type = $numObj->getType();
		return $number*$this->type;
	}
	
	abstract public function getDefaultType();
	
}
