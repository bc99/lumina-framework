<?php

namespace lumina\nodes\html\forms;

use lumina\nodes\html\Node;
use lumina\nodes\html\Tag;

use lumina\nodes\html\forms\Validator;

/**
 * @method Element create()
 */
class Element extends Tag{
	
	private $validators = array();
	
	private $inputTextTypes = array(
		// HTML4, XHTML1.x
		'text',
		
		// HTML5
		'number','range','email',
		'date','month','week','time',
		'datetime','datetime-local',
		'search','color',
	);
	
	/**
	 * @var Form
	 */
	private $form = null;
	
	/**
	 * @var Node
	 */
	private $parent = null;
	
	public function __construct($tagName, $type = null, $name = null, $eventClass = null){
		parent::__construct($tagName);
		if(!is_null($type)) $this->setAttr('type', $type);
		if(!is_null($name)) $this->setAttr('name', $name);
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
		
		if(!empty($this->validators)){
			reset($this->validators);
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
	
	/**
	 * @param mixed $value
	 * @return Element
	 */
	public function setValue($value){
		$tagName = $this->getTagName();
		if($tagName == 'input'){
			$type = $this->getAttr('type');
			if(in_array($type, array('checkbox','radio'))){
				$attrValue = $this->getAttr('value');
				if($attrValue == $value){
					$this->setAttr('checked', 'checked');
				}
			}
			else if(in_array($type, $this->inputTextTypes)){
				$this->setAttr('value', $value);
			}
		}
		else if($tagName == 'textarea'){
			$this->text($value);
		}
		return $this;
	}
	
}