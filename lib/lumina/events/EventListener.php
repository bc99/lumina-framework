<?php

namespace lumina\events;

interface EventListener{
	
//	/**
//	 * Allgemeingültiger Listener, auf den ausgewichen
//	 * wird, wenn ein Listener fehlen sollte.
//	 * @param Event $event
//	 */
//	public function onTrigger($event);
	
	/**
	 * Gibt alle Eventtypen zurück, auf die der Listener hören soll.
	 * @return array
	 */
	public function getListenerNames();
}