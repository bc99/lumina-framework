<?php

namespace lumina\nodes\html\forms;

use lumina\nodes\html\forms\events\FormEvent;

use lumina\nodes\html\Node;
use lumina\nodes\html\Tag;

use lumina\nodes\NodeList;

use lumina\events\EventHandler;
use lumina\events\EventListener;

/**
 * @method Form create()
 * @method Form singleton()
 * @method string className()
 */
class Form extends Tag{
	
	public function __construct($method = "post", $action = "", $eventClass = null){
		if(is_null($eventClass)) $eventClass = FormEvent::className();
		parent::__construct('form', $eventClass);
		$this->setEventHandler(EventHandler::create($this));
		$this->setAttr('method', $method)->setAttr('action', $action);
	}
	
	/**
	 * @param mixed $listener
	 * @return Form
	 */
	public function addListenerObject(EventListener $listener){
		$this->eventHandler->addListenerObject($listener);
		return $this;
	}
	
	/**
	 * @param mixed $listener
	 * @return Form
	 */
	public function addBeforeValidateListener($listener){
		$this->eventHandler->addEventListener('beforeValidate', $listener);
		return $this;
	}
	
	/**
	 * @param mixed $listener
	 * @return Form
	 */
	public function addSuccessListener($listener){
		$this->eventHandler->addEventListener('success', $listener);
		return $this;
	}
	
	/**
	 * @param mixed $listener
	 * @return Form
	 */
	public function addCompleteListener($listener){
		$this->eventHandler->addEventListener('complete', $listener);
		return $this;
	}
	
	/**
	 * @param mixed $listener
	 * @return Form
	 */
	public function addErrorListener($listener){
		$this->eventHandler->addEventListener('error', $listener);
		return $this;
	}
	
	private function validateRecursive(Node $node, $input){
		if(!$node->getChildren()) return false;
		$iterator = $node->getChildren()->getIterator();
		$status = true;
		if($iterator->valid()){
			do{
				$curr = $iterator->current();
				if($curr instanceof Tag){
					$curr->setForm($this);
					$curr->setParent($node);
					
					$name_complete = $curr->getAttr('name');
					preg_match("/^([\w\d\_-]+)/", $name_complete, $matches);
					if(isset($matches[0])){
						$name = $matches[0];
						if(!isset($input[$name])){
							$input[$name] = null;
						}
						$status &= $curr->isValid($input[$name]);
					}
				}else if($curr instanceof Node){
					$status &= $this->validateRecursive($curr, $input);
				}
				$iterator->next();
			}while($iterator->valid());
		}
		return $status;
	}
	
	public function isValid($input){
		$this->trigger('beforeValidate');
		
		// Rekursive Validierung des Formulars
		$status = $this->validateRecursive($this, $input);
		
		if($status == true){
			$this->trigger('success');
		}else{
			$this->trigger('error');
		}
		
		$this->trigger('complete', array('status' => $status));
		
		return $status;
	}
	
}