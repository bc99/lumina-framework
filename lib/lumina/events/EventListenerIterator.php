<?php

namespace lumina\events;

class EventListenerIterator extends \ArrayObject{
	
	public function addAll($listener){
		if(($listenerIndex = $this->indexOf($listener)) === false){
			$this->append(array($listener, $listener->getListenerNames()));
		}
	}
	
	public function removeAll($listener){
		if(($listenerIndex = $this->indexOf($listener)) === false) return;
		unset($this[$listenerIndex]);
	}
	
	public function remove($type, $listener){
		if(($listenerIndex = $this->indexOf($listener)) === false) return;
		list($foundListener, $types) = $this->offsetGet($listenerIndex);
		
		// Suche den zu löschenden Eventtyp
		if($typeIndex = array_search($type, $types)){
			unset($types[$typeIndex]);
		}
		
		// Falls alle Typen entfernt wurden, kann
		// der komplette Listener gelöscht werden.
		if(empty($types)){
			unset($this[$listenerIndex]);
		}
		
		// Andernfalls wird der Eintrag bearbeitet.
		else{
			$this[$listenerIndex][1][] = $types;
		}
	}
	
	public function add($type, $listener){
		
		// Ist der Listener noch nicht vorhanden, wird
		// dieser angelegt.
		if(($listenerIndex = $this->indexOf($listener)) === false){
			$this->append(array($listener, array($type)));
		}
		
		// Ansonsten wird nur der Typ hinzugefügt.
		else{
			$this[$listenerIndex][1][] = $type;
		}
	}
	
	/**
	 * Findet die Index-Nummer eines Event-Listeners.
	 * @param mixed $listener
	 * @return integer|false
	 */
	public function indexOf($listener){
		$iterator = $this->getIterator();
		if($iterator->valid()) do{
			$curr = $iterator->current();
			if($listener === $curr[0]){
				return $iterator->key();
			}
			$iterator->next();
		}while($iterator->valid());
		return false;
	}
	
}

