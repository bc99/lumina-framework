<?php

namespace lumina\nodes\html\forms\events;

use lumina\events\EventListener;

abstract class FormListener implements EventListener{
	abstract public function onFormBeforeValidate($event);
	abstract public function onFormSuccess($event);
	abstract public function onFormComplete($event);
	abstract public function onFormError($event);
	
	public function getListenerNames(){
		return array('beforeValidate', 'success', 'complete', 'error');
	}
}
