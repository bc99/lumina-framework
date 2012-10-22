<?php

namespace lumina\core;

use lumina\core\datatypes\ArrayType;
use lumina\core\datatypes\StringType;

class Request extends ArrayType{
	
	/**
	 * Dem Konstruktor können beliebig viele Argumente übergeben werden,
	 * die dann der Reihenfolge nach zusammengefasst werden.
	 * @param array $array
	 */
	public function __construct($array, $_ = null){
		$argn = func_num_args();
		$args = func_get_args();
		if($argn == 1){
			parent::__construct($args[0]);
		}else if($argn > 1){
			parent::__construct(call_user_func_array('array_merge', $args));
		}else{
			throw new \Exception('Dem Request-Objekt muss mindestens ein Array übergeben werden.');
		}
	}
	
	/**
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get($key){
		return $this->get($key);
	}
	
	/**
	 * @param mixed $key
	 * @param mixed $alternative
	 */
	public function __call($key, $alternative = null){
		return $this->get($key, $alternative);
	}
	
	/**
	 * @param mixed $key
	 * @param mixed $alternative
	 * @return StringType
	 */
	public function getString($key, $alternative = null){
		$item = $this->get($key, $alternative);
		return StringType::singleton($item);
	}
	
	/**
	 * @param mixed $key
	 * @param mixed $alternative
	 * @return mixed
	 */
	public function get($key, $alternative = null){
		if(isset($this[$key])){
			// print $key;
			return $this[$key];
		}
		return $alternative;
	}
	
	/**
	 * Gibt eine Liste ausgesuchter Keys zurück.
	 * @param string $key1
	 * @param string $key2
	 * @param string $_
	 */
	public function getArray($key1, $key2 = null, $_ = null){
		$keys = func_get_args();
		$array = array();
		
		foreach($keys as &$key){
			if(isset($this[$key]))
				$array[$key] =& $this[$key];
		} unset($key);
		
		return $array;
	}
	
}