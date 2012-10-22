<?php

namespace lumina\nodes\html\forms\elements;

class PasswordElement extends TextElement{
	
	public function __construct($name, $eventClass = null){
		parent::__construct($name, $eventClass);
		$this->setAttr('type', 'password');
		$this->setId($name);
	}
	
}