<?php

namespace lumina\nodes\html;

use lumina\nodes\NodeList;

/**
 * @method Node create(string $tagName, boolean $isXhtml, Option $option, NodeList $nodeList)
 * @method Node singleton(string $tagName, boolean $isXhtml, Option $option, NodeList $nodeList)
 * @method String className()
 * @method Node setOption(Option $option)
 * @method Node setChildren(NodeList $children)
 * @method Node append(Node $node)
 * @method Node prepend(Node $node)
 * @method Node clear()
 * @property Option $option
 */
class Node extends \lumina\nodes\Node{
	const HTML_4_STRICT = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
	const HTML_4_FRAME = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
	const HTML_4_TRANS = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	
	const XHTML_10_STRICT = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	const XHTML_10_FRAME = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
	const XHTML_10_TRANS = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	const XHTML_11 = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
	
	const HTML_5 = '<!DOCTYPE html>';
	
	const XHTML_ENABLED = true;
	const XHTML_DISABLED = false;
	
	private $singletonTags = array('meta','link','base','col','area','param','br','hr','input');
	private $attrs = array();
	
	/**
	 * @param string $tagName
	 * @param boolean $isXhtml
	 * @param Option|null $option
	 * @param NodeList|array $nodeList
	 */
	public function __construct($tagName, $isXhtml = self::XHTML_DISABLED, $option = null, $nodeList = null){
		$this->setOption(is_null($option) ? Option::create() : $option);
		$this->option->setTagName($tagName);
		$this->option->setXhtmlEnabled($isXhtml);
		if(!is_null($nodeList)) $this->setChildren($nodeList);
	}
	
	public function render(){
		$attrs = $this->option->get('attrs',true);
		
		$out = '';
		$out = "<{$this->option->getTagName()}";
		
		if($attrs !== false){
			foreach($attrs as $name => $value){
				$out .= ' ' . $name . '="' . htmlspecialchars($value, ENT_COMPAT, $this->option->getEncoding()) . '"';
			}
		}
		if(!is_null($this->children) && $this->children->count() > 0){
			$iterator = $this->children->getIterator();
			do{
				$curr = $iterator->current();
				if(!is_null($curr) && is_object($curr) && (is_a($curr, Node::className()) || is_subclass_of($curr, Node::getName()))){
					$curr->setXhtmlEnabled($this->option->isXhtml());
				}
				$curr = $iterator->next();
			}while($iterator->valid());
			if($this->option->getTagName() == "script"){
				if($this->option->isXhtml()){
					$out .= ">\n// <![CDATA[\n{$this->children->render()}\n// ]]>\n</{$this->option->getTagName()}>";
				}else{
					$out .= "><!--\n{$this->children->render()}\n--></{$this->option->getTagName()}>";
				}
			}else{
				$out .= ">{$this->children->render()}</{$this->option->getTagName()}>";
			}
		}else if(!in_array($this->option->getTagName(), $this->singletonTags)){
			$out .= "></{$this->option->getTagName()}>";
		}else{
			$out .= $this->option->isXhtml() ? "/>" : ">";
		}
		return $out;
	}
	
	public function __toString(){
		return $this->render();
	}
	
	/**
	 * @param boolean $str
	 * @return Node
	 */
	public function setXhtmlEnabled($enable){
		$this->option->setXhtmlEnabled($enable);
		return $this;
	}
	
	public function getTagName(){
		return $this->option->getTagName();
	}
	
	/**
	 * Es kann entweder der Tag-Name "div", "p", etc. oder
	 * das entsprechende Node-Objekt übergeben werden.
	 * @param Node|string $node
	 * @return Node
	 */
	public function wrap($node){
		if(is_string($node)) $node = Node::create($node);
		if(is_array($node) || $node instanceof NodeList){
			throw new \Exception('node lists are not allowed for this function.');
		}
		$parent = $this->getParent();
		if($parent !== false){
			$children = $parent->getChildren();
			$thisIndex = $parent->getChildren()->indexOf($this);
			unset($children[$thisIndex]);
			if(!$parent->getChildren()->indexOf($node)){
				$children->setNodes($parent, array($node));
			}
		}
		$node->setChildren($this);
		return $node;
	}
	
	/**
	 * @param string $str
	 * @return Node
	 */
	public function html($str){
		$this->clear()->children->append($str);
		return $this;
	}
	
	/**
	 * @param string $str
	 * @return Node
	 */
	public function text($str){
		$this->clear()->children->append(htmlentities($str, null, $this->option->get("encoding")));
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
	
	/**
	 * @param string $name
	 * @return string
	 */
	public function getAttr($name){
		return $this->option->getAttr($name);
	}
	
	/**
	 * @param string $name
	 * @return Node
	 */
	public function setId($name){
		$this->option->setAttr('id', $name);
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return string
	 */
	public function getId(){
		return $this->option->getAttr('id');
	}
	
	/**
	 * @param string $name
	 * @return Node
	 */
	public function removeAttr($name){
		if(isset($this->attrs[$name]))
			unset($this->attrs[$name]);
		return $this;
	}
	
	/**
	 * @param string $className
	 * @return Node
	 */
	public function addClass($className){
		$this->option->addClass($className);
		return $this;
	}
	
	/**
	 * @param string $className
	 * @return Node
	 */
	public function removeClass($className){
		$this->option->removeClass($className);
		return $this;
	}
	
	/**
	 * @param string $className
	 * @return boolean
	 */
	public function hasClass($className){
		return $this->option->hasClass($className);
	}
	
}