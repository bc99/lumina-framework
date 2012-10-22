<?php

namespace lumina\nodes\html\forms\elements;

use lumina\nodes\html\Node;
use lumina\nodes\html\forms\Element;

/**
 * @method TextElement create()
 * @method TextElement singleton()
 */
class TextElement extends Element{
	
	public function __construct($name, $eventClass = null){
		parent::__construct('input', 'text', $name, $eventClass);
		$this->setId($name);
		$this->addClass('lumina-text');
	}
	
	/**
	 * @param string $text
	 * @return TextElement
	 */
	public function setLabel($text){
		$label = Node::create('label')->text($text)
			->setAttr('for', $this->getAttr('id'));
		$this->setOptionEntry('formLabel', $label);
		$parent = $this->getParent();
		return $this;
	}
	
	/**
	 * @return Node|false
	 */
	public function getLabel(){
		return $this->getOptionValue('formLabel');
	}
	
	public function render(){
		$label = $this->getLabel();
		if($label !== false && $label->getOptionValue('parent') == null){
			$p = Node::create('p')->setXhtmlEnabled($this->option->isXhtml());
//			echo "Tag {$this->option->getTagName()}[type={$this->getAttr('type')}] ist XHTML: " . ($this->option->isXhtml()?'ja':'nein').'<br/>';
			return $label->addClass('lumina-text')->wrap($p)
					->append($this)->render();
		}
		return parent::render();
	}
	
}