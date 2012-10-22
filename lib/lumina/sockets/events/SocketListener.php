<?php

namespace lumina\sockets\events;

use lumina\core\Object;
use lumina\events\EventListener;
use lumina\forks\Runnable;

abstract class SocketListener extends Object
		implements EventListener, Runnable{
	
	public function getListenerNames(){
		return array('connect', 'disconnect', 'error');
	}
	
	abstract public function onConnect($event);
	abstract public function onDisconnect($event);
	abstract public function onError($event);
	
}
