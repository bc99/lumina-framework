<?php

namespace lumina\nodes\html;

use lumina\events\Event;
use lumina\events\EventHandler;

class Tag extends Node{
	
	protected $eventClass = null;
	
	/**
	 * @var EventHandler
	 */
	protected $eventHandler = null;
	
	public function __construct($tagName, $eventClass = null){
		parent::__construct($tagName);
		$this->eventClass = class_exists($eventClass) ? $eventClass : Event::className();
		$this->eventHandler = EventHandler::singleton();
	}
	
	public function setEventHandler(EventHandler $handler){
		$this->eventHandler = $handler;
	}
	
	/**
	 * @param function|object $listener
	 * @return Tag
	 */
	public function addBeforeRenderListener($listener){
		$this->eventHandler->addEventListener('beforeRender', $listener);
		return $this;
	}
	
	/**
	 * @param function|object $listener
	 * @return Tag
	 */
	public function addAfterRenderListener($listener){
		$this->eventHandler->addEventListener('afterRender', $listener);
		return $this;
	}
	
	/**
	 * @param string $type
	 * @param function|object $listener
	 * @return Tag
	 */
	public function addEventListener($type, $listener){
		$this->eventHandler->addEventListener($type, $listener);
		return $this;
	}
	
	/**
	 * @param string $type
	 * @param array $addition
	 * @return Tag
	 */
	public function trigger($type, array $addition = array()){
		$this->eventHandler->trigger($type, $this, $addition, $this->eventClass);
		return $this;
	}
	
	public function render(){
		$this->trigger('beforeRender');
		$out = parent::render();
		$this->trigger('afterRender');
		return $out;
	}
	
}