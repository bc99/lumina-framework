<?php

namespace lumina\nodes;

use lumina\core\Object;
use lumina\nodes\Option;
use lumina\nodes\NodeList;

/**
 * @method Node create($option, $nodeList)
 * @method Node singleton($option, $nodeList)
 * @method String className()
 */
class Node extends Object{
	
	/**
	 * @var NodeList
	 */
	protected $children = null;
	
	/**
	 * @var Option
	 */
	protected $option = null;
	
	
	/**
	 * @param Option $option
	 * @param NodeList|array $nodeList
	 */
	public function __construct($option = null, $nodeList = array()){
		$this->setOption($option);
		$this->setChildren($nodeList);
	}
	
	public function __toString(){
		return $this->render();
	}
	
	public function render(){
		return $this->children->render();
	}
	
	/**
	 * Gibt das Eltern-Element des Knotens zurück.
	 * Falls kein Eltern-Element existieren sollte,
	 * wird FALSE zurück gegeben.
	 * @return Node|false
	 */
	public function getParent(){
		return $this->getOptionValue('parent');
	}
	
	/**
	 * @param array $array
	 * @return NodeList
	 */
	protected function createNodeList(array $nodes = array()){
		return new NodeList($this, $nodes);
	}
	
	/**
	 * Definiert eine neue Option, die von diesem
	 * Knoten genutzt werden soll.
	 * @param Option $option
	 * @return Node
	 */
	public function setOption(Option $option){
		if($option instanceof Option) $this->option = $option;
		else $this->option = Option::create();
		return $this;
	}
	
	/**
	 * Gibt einen in den Optionen gespeicherten Wert zurück.
	 * Wenn der Wert nicht existiert, wird FALSE zurück gegeben.
	 * @param string $name
	 * @return mixed|false
	 */
	public function __get($name){
		return $this->option->get($name, true);
	}
	
	public function __set($name, $value){
		$this->option->set($name, $value);
	}
	
	/**
	 * Gibt den Wert einer Option zurück oder FALSE
	 * @return mixed|false
	 */
	public function getOptionValue($name){
		return $this->option->get($name, true);
	}
	
	/**
	 * Ändert den Wert einer Option
	 * @param string $name
	 * @param mixed $value
	 * @return Node
	 */
	public function setOptionEntry($name, $value){
		$this->option->set($name, $value);
		return $this;
	}
	
	/**
	 * Löscht alle Kindelemente dieses Knotens und fügt
	 * die übergebenen Kindelemente als neue Kinder ein.
	 * @param mixed $children
	 * @return Node
	 */
	public function setChildren($children){
		if(is_string($children) || $children instanceof Node){
			$children = array($children);
		}
		if($this->children === null){
			$this->children = $this->createNodeList();
		}
		if(is_array($children)){
			$this->clear()->children->setNodes($this, $children);
		}
		else if($children instanceof NodeList){
			$this->children = $children;
		}
		else throw new NodeListException("Ungültiger Datentyp: Erwartet wurde entweder ein String, ein Array, ein Node-Objekt oder eine NodeList.");
		$this->children->filter();
		return $this;
	}
	
	/**
	 * Gibt die NodeList aller Kindelemente zurück.
	 * @return NodeList
	 */
	public function getChildren(){
		if(is_null($this->children)) $this->createNodeList();
		return $this->children;
	}
	
	/**
	 * Gibt ein Kindelement anhand des Indexes zurück.
	 * Sollte das Kindelement nicht existieren, wird
	 * NULL zurückgegeben.
	 * @param integer $index
	 * @return Node|false
	 */
	public function getItem($index){
		if(!is_null($this->children) && isset($this->children[$index])){
			return $this->children[$index];
		}
		return false;
	}
	
	/**
	 * Fügt ein Element am Ende hinzu.
	 * @param Node $node
	 * @return Node
	 */
	public function append($node){
		if(is_null($this->children)){
			$this->setChildren(array($node));
		}else{
			$this->children->append($node);
			$this->children->filter();
		}
		return $this;
	}
	
	/**
	 * Fügt ein Element an den Anfang hinzu.
	 * @param Node $node
	 * @return Node
	 */
	public function prepend($node){
		if(is_null($this->children)){
			$this->clear()->setChildren($node);
		}else{
			$arr = $this->children->toArray();
			array_unshift($arr, $node);
			$this->clear()->children->setNodes($this, $arr);
		}
		return $this;
	}
	
	/**
	 * Entfernt alle Kindelemente.
	 * @return Node
	 */
	public function clear(){
		if(!is_null($this->children)){
			$this->children->fromArray(array());
		}else{
			$this->children = $this->createNodeList();
		}
		return $this;
	}
}