<?php

namespace lumina\nodes\html;

/**
 * @method Option create()
 */
class Option extends \lumina\nodes\Option{
	
	public function __construct($doctype = Node::XHTML_10_STRICT, $encoding = "UTF-8"){
		parent::__construct();
		$this->setDoctype($doctype);
		$this->setEncoding($encoding);
	}
	
	/**
	 * @param string $name
	 * @return Option
	 */
	public function setTagName($name){
		$this->set('tag', strtolower($name));
		return $this;
	}
	
	/**
	 * @param string $doctype
	 * @return Option
	 */
	public function setDoctype($doctype){
		$this->set('doctype', $doctype);
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getDoctype(){
		return $this->get("doctype", true);
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
	 * @return string
	 */
	public function getTagName(){
		return $this->get('tag');
	}
	
	public function setXhtmlEnabled($enable){
		$this->set('xhtml', (boolean) $enable);
		return $this;
	}
	
	public function isXhtml(){
		return $this->get('xhtml', true);
	}
	
	/**
	 * @param string $name
	 * @param string $value
	 * @return Option
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
			return null;
		}
		return $attrs[$name];
	}
	
	/**
	 * @param string $name
	 * @return Option
	 */
	public function removeAttr($name){
		$attrs = $this->get('attrs', true);
		if(isset($attrs[$name]))
			unset($attrs[$name]);
		return $this;
	}
	
	/**
	 * @param string $className
	 * @return Option
	 */
	public function addClass($className){
		$classes = explode(' ', $this->getAttr('class'));
		$classes[] = trim($className);
		$this->setAttr('class', trim(implode(' ', array_unique($classes))));
		return $this;
	}
	
	/**
	 * @param string $className
	 * @return Option
	 */
	public function removeClass($className){
		$classes = explode(' ', $this->getAttr('class'));
		if($index = array_search(trim($className), $classes)){
			unset($classes[$index]);
		}
		$this->setAttr('class', trim(implode(' ', array_unique($classes))));
		return $this;
	}
	
	/**
	 * @param string $className
	 * @return boolean
	 */
	public function hasClass($className){
		$classes = explode(' ', $this->getAttr('class'));
		return in_array(trim($className), $classes);
	}
	
}
