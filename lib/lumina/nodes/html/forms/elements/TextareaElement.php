<?php

namespace lumina\nodes\html\forms\elements;

use lumina\nodes\html\forms\Form;
use lumina\nodes\html\forms\Validator;
use lumina\nodes\html\Node;
use lumina\nodes\html\Tag;

class TextareaElement extends Tag{
	
	private $validators = array();
	
	/**
	 * @var Form
	 */
	private $form = null;
	
	/**
	 * @var Node
	 */
	private $parent = null;
	
	public function __construct($name = null, $cols = 60, $rows = 5){
		parent::__construct("textarea");
		$this->setAttr('cols', $cols)->setAttr('rows', $rows);
		if(!is_null($name)) $this->setAttr('name', $name)->setId($name);
	}
	
	/**
	 * @param Form $form
	 * @return Element
	 */
	public function setForm(Form $form){
		$this->form = $form;
		return $this;
	}
	
	/**
	 * @param Node $node
	 * @return Element
	 */
	public function setParent(Node $node){
		$this->parent = $node;
		return $this;
	}
	
	/**
	 * @param Validator $validator
	 * @return Element
	 */
	public function addValidator(Validator $validator){
		$this->validators[] = $validator;
		array_unique($this->validators);
		return $this;
	}
	
	/**
	 * @param mixed $value
	 * @return integer Statuscode des Formulars
	 */
	public function isValid($value){
		$this->setValue($value);
		$status = true;
		
		reset($this->validators);
		if(!empty($this->validators)){
			do{
				$curr = current($this->validators);
				$result = $curr->isValid($this, $value);
				if($curr !== false && !$result){
					$this->trigger('fieldError', array(
						'response' => $curr->getResponse(),
						'parent' => $this->parent
					));
				}
				$status &= $result;
			}while(next($this->validators) !== false);
		}
		return $status;
	}
	
	public function getValue(){
		return $this->getItem(0);
	}
	
	/**
	 * @param mixed $value
	 * @return Element
	 */
	public function setValue($value){
		$this->text($value);
		return $this;
	}
	
	/**
	 * @param string $text
	 * @return TextElement
	 */
	public function setLabel($text){
		$label = Node::create('label')->text($text);
		if($this->getId() != "") $label->setAttr('for', $this->getId());
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