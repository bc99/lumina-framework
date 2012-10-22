<?php

namespace lumina\sockets\events;

use lumina\forks\Runnable;
use lumina\forks\Fork;
use lumina\sockets\events\SocketListener;

class SocketFork extends Fork{
	
	private $parameters = null;
	
	public function __construct($runnable, $type, array $parameters = array()){
		parent::__construct($runnable);
		$this->parameters = $parameters;
	}
	
	public function run(){
		if($this->runnable instanceof SocketListener){
			$eventType = "on" . ucwords(strtolower(array_shift($type)));
			$rm = new \ReflectionMethod(get_class($this->runnable), $eventType);
			$rm->invokeArgs($this->runnable, $this->parameters);
		}
	}
	
}