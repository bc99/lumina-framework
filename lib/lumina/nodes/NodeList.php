<?php

namespace lumina\nodes;

use lumina\core\datatypes\ArrayType;

class NodeList extends ArrayType{
	public function __construct($parent, $nodes){
		$this->setNodes($parent, $nodes);
	}
	
	public function setNodes(Node $parent, array $nodes){
		foreach($nodes as $node){
			if($node instanceof Node){
				$node->setOptionEntry('parent', $parent);
			}
			$this->append($node);
		}
	}
	
	public function indexOf($node){
		$iterator = $this->getIterator();
		if($iterator->valid()){
			do{
				$curr = $iterator->current();
				if($curr === $node) return $iterator->key();
				
				$iterator->next();
			}while($iterator->valid());
		}
		return false;
	}
	
	public function render(){
		$out = '';
		foreach($this->getIterator() as $node){
			if($node instanceof Node){
				$out .= $node->render();
			}else{
				$out .= (string) $node;
			}
		}
		return $out;
	}
	public function filter(){
		$arr = $this->toArray();
		$this->fromArray(array_filter($arr));
	}
	public function __toString(){
		return $this->render();
	}
}