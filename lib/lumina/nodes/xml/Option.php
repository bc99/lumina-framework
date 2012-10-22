<?php

namespace lumina\nodes\xml;

/**
 * @method Xml\Option create()
 */
class Option extends \lumina\nodes\Option{
	
	public function __construct($encoding = "UTF-8"){
		parent::__construct();
		$this->setEncoding($encoding);
	}
	
	/**
	 * @param string $encoding
	 * @return Option
	 */
	public function setEncoding($encoding){
		$this->set('encoding', $encoding);
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getEncoding(){
		return $this->get('encoding',true);
	}
	
	/**
	 * @param string $name
	 * @return Xml\Option
	 */
	public function setTagName($name){
		$this->set('tag', $name);
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getTagName(){
		return $this->get('tag');
	}
	
	/**
	 * @param string $name
	 * @param string $value
	 * @return Xml\Option
	 */
	public function setAttr($name, $value){
		$newAttr = array($name => $value);
		if(($attrs = $this->get('attrs', true)) === false){
			$attrs = $newAttr;
		}else{
			$attrs = array_merge($attrs, $newAttr);
		}
		$this->set('attrs', $attrs);
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return string
	 */
	public function getAttr($name){
		$attrs = $this->get('attrs',true);
		if($attrs === false || !isset($attrs[$name])){
			print_r($attrs);
			return null;
		}
		return $attrs[$name];
	}
	
	/**
	 * @param string $name
	 * @return Xml\Option
	 */
	public function removeAttr($name){
		$attrs = $this->get('attrs', true);
		if(isset($attrs[$name]))
			unset($attrs[$name]);
		return $this;
	}
	
}
