<?php

namespace lumina\core\datatypes;

use lumina\core\Object;

class ArrayType extends Object
		implements \ArrayAccess, \IteratorAggregate, \Countable{
	
	/**
	 * @var \ArrayIterator
	 */
	private $iterator = null;
	
	public function __construct($array = array()){
		$this->fromArray(!empty($array) ? $array : array());
	} 
	
	/**
	 * @param array $data
	 * @return ArrayType
	 */
	public function fromArray(array $array){
		$this->iterator = new \ArrayIterator($array);
		return $this;
	}
	
	public function toArray(){
		return $this->iterator->getArrayCopy();
	}
	
	/**
	 * shift an element from the beginning and reduce the array.
	 * @return mixed
	 */
	public function shift(){
		$arr = $this->toArray();
		$item = array_shift($arr);
		$this->fromArray($arr);
		return $item;
	}
	
	/**
	 * pop an element from the end and reduce the array.
	 * @return mixed
	 */
	public function pop(){
		$arr = $this->toArray();
		$item = array_pop($arr);
		$this->fromArray($arr);
		return $item;
	}
	
	/**
	 * prepends a value to the beginning
	 * @param mixed $value
	 */
	public function prepend($value){
		$arr = $this->toArray();
		array_unshift($arr, $value);
		$this->fromArray($arr);
	}
	
	/**
	 * appends a value to the end
	 * @param mixed $value
	 */
	public function append($value){
		if(is_null($this->iterator))
			$this->fromArray(array($value));
		else
			$this->iterator->append($value);
	}
	
	public function count(){
		return $this->iterator->count();
	}
	
	public function getIterator(){
		return $this->iterator;
	}
	
	/**
	 * @param offset
	 */
	public function offsetExists($offset) {
		return $this->iterator->offsetExists($offset);
	}

	/**
	 * @param offset
	 */
	public function offsetGet($offset) {
		if(!$this->iterator->offsetExists($offset))
			throw new \Exception("Offset $offset does not exist!");
		return $this->iterator->offsetGet($offset);
	}

	/**
	 * @param offset
	 * @param value
	 */
	public function offsetSet($offset, $value) {
		$this->iterator->offsetSet($offset, $value);
	}

	/**
	 * @param offset
	 */
	public function offsetUnset($offset) {
		if($this->offsetExists($offset)) $this->iterator->offsetUnset($offset);
	}
	
}