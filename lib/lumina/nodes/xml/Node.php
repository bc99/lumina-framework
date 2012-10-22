<?php

namespace lumina\nodes\xml;
use lumina\nodes\NodeList;

/**
 * @method Node create()
 * @method Node setOption(Option)
 * @method Node setChildren(string|Node|NodeList)
 * @method Node append(Node|string)
 * @method Node prepend(Node|string)
 * @method Node clear()
 * @property Option $option
 */
class Node extends \lumina\nodes\Node{
	
	public function __construct($tagName, $encoding = 'UTF-8'){
		parent::__construct();
		$this->option->setTagName($tagName);
		$this->option->setEncoding($encoding);
		$this->setOptionEntry('content-type', 'text/xml');
	}
	
	/**
	 * @param Option $option
	 * @return Node
	 */
	public function setOption(Option $option){
		if($option instanceof Option) $this->option = $option;
		else $this->option = Option::create();
		return $this;
	}
	
	public function render(){
		$attrs = $this->option->get('attrs',true);
		
		$out = '';
		$out = "<{$this->option->getTagName()}";
		
		if($attrs !== false){
			foreach($attrs as $name => $value){
				$out .= ' ' . $name . '="' . htmlspecialchars($value, ENT_COMPAT, $this->option->get('encoding',true)) . '"';
			}
		}
		if($this->children->count() > 0){
			$out .= ">{$this->children->render()}</{$this->option->getTagName()}>";
		}else{
			$out .= "/>";
		}
		
		$xmlheader = '';
		if($this->getParent() === false){
			$xmlheader = "<?xml version=\"1.0\" encoding=\"{$this->option->getEncoding()}\"?>";
		}
		
		return $xmlheader . $out;
	}
	
	public function __toString(){
		return $this->render();
	}
	
	/**
	 * @param string $str
	 * @return Node
	 */
	public function text($str){
		$this->clear()->children->append($str);
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return Node
	 */
	public function setAttr($name, $value){
		$this->option->setAttr($name, $value);
		return $this;
	}
	
	public function getAttr($name){
		return $this->option->getAttr($name);
	}
	
	/**
	 * @param string $name
	 * @return Node
	 */
	public function removeAttr($name){
		$this->option->removeAttr($name);
		return $this;
	}
}