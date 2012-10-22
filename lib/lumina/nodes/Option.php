<?php

namespace lumina\nodes;

use lumina\core\datatypes\ArrayType;

class Option extends ArrayType{
	
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return Option
	 */
	public function set($name, $value){
		$this[$name] = $value;
		return $this;
	}
	
	/**
	 * Gibt den Wert der Option zurück, oder im Fehlerfall FALSE,
	 * sofern explizit keine Exception geworfen werden soll.
	 * @param string $name
	 * @param boolean $noThrow
	 * @return mixed|false
	 * @throws OptionException
	 */
	public function get($name, $noThrow = false){
		if(isset($this[$name])){
			return $this[$name];
		}
		else if(!$noThrow){
			throw new OptionException("The option $name does not exist!");
		}
		return false;
	}
	
}