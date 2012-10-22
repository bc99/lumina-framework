<?php

namespace lumina\nodes\html;

use lumina\events\EventListener;

abstract class TagListener implements EventListener{
	abstract public function onBeforeRender($event);
	abstract public function onAfterRender($event);
	
	public function getListenerNames(){
		return array('beforeRender', 'afterRender');
	}
}
