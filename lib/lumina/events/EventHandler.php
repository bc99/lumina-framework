<?php

namespace lumina\events;

use lumina\core\Object;
use lumina\events\EventListener;
use lumina\events\EventListenerIterator;
use lumina\events\EventException;

/**
 * @method EventHandler singleton()
 * @method EventHandler create()
 */
class EventHandler extends Object{
	
	/**
	 * @var EventListenerIterator
	 */
	private $listeners = null;
	
	protected $root = null;
	
	public function __construct($root = null, $eventListenerClass = null){
		if(!is_null($root)) $this->root = $root;
		$this->listeners = is_null($eventListenerClass)
				? new EventListenerIterator() : new $eventListenerClass();
	}
	
	public function addListenerObject(EventListener $listener){
		$this->listeners->addAll($listener);
	}
	
	public function removeListenerObject(EventListener $listener){
		$this->listeners->removeAll($listener);
	}
	
	public function addEventListener($type, $listener){
		$this->listeners->add($type, $listener);
	}
	
	public function removeEventListener($type, $listener){
		$this->listeners->remove($type, $listener);
	}
	
	/**
	 * Steuert alle dem Typ zugehörige EventListener an und
	 * übergibt ihnen das entsprechende Event.
	 * @param string $type
	 * @param mixed|null $element
	 * @param array $addition
	 * @param string|null $eventClass
	 * @return array Alle gesammelten Exceptions, die nicht weiter verarbeitet werden konnten.
	 */
	public function trigger($type, $element = null, array $addition = array(), $eventClass = null){
		$iterator = $this->listeners->getIterator();
		if(!$iterator->valid()) return;
		
		if(is_null($element)) $element = $this;
		if(is_null($eventClass)) $eventClass = Event::className();
		
		// Initialisierung des Abfang-Arrays für Exceptions.
		$exceptions = array();
		do{
			list($listener, $types) = $iterator->current();
			
			foreach(array_intersect($types, explode(' ', $type)) as $t){
				// Initialisieren des Events. Es kann ein über
				// den Konstruktor individuell definiertes Event
				// sein, oder ein Standard-Event.
				$event = new $eventClass($t, $this->root, $element, $addition);
				$parameters = array_merge(array($event), $addition);
				
				try{
					
					// Handelt es sich um eine Listener-Klasse, wird eine
					// Methode mit Namen on<Typ>(Event) aufgerufen. Sollte
					// die Methode nicht existieren, wird die allgemeingültige
					// Methode onTrigger aufgerufen.
					if(is_object($listener) && !($listener instanceof \Closure)){
						$eventType = "on" . ucwords(strtolower($event->type));
						
						if(method_exists($listener, $eventType)){
							$rm = new \ReflectionMethod(get_class($listener), $eventType);
							$rm->invokeArgs($listener, $parameters);
						}
						
						// Alternativ kann auch ein globaler Listener
						// angesprochen werden, sollte keine Methode
						// für dieses Event bereitstehen.
						else if(method_exists($listener, 'onTrigger')){
							$rm = new \ReflectionMethod(get_class($listener), 'onTrigger');
							$rm->invokeArgs($listener, $parameters);
						}
					}
					
					// Handelt es sich um eine Funktion, wird
					// das Event dieser Funktion übergeben.
					else if(is_callable($listener)){
						call_user_func_array($listener, $parameters);
					}
					
					// Wir haben es mit einer fremden Art zu tun ;-)
					else{
						throw new EventException('Ein Listener vom Typ "' . $event->type . '" ist ungültig. Prüfe, ob es sich um eine Funktion oder ein Objekt handelt.');
					}
					
				}catch(\Exception $e){
					// Fehler in onError-Methode behandeln
					if(is_object($listener) && method_exists($listener, "onError")){
						$event->addition['exception'] = $e;
						$rm = new \ReflectionMethod(get_class($listener), 'onError');
						$rm->invokeArgs($listener, $parameters);
					}
					
					// sammle alle anderen (unverwertbaren) Exceptions.
					else{
						$exceptions[] = $e;
					}
				}
			}
			
			$iterator->next();
		} while($iterator->valid());
		
		if(!empty($exceptions)){
			$this->trigger('exception', $this, $exceptions, Event::className());
		}
		return $exceptions;
	}

}