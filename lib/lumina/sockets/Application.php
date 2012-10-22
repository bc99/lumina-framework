<?php

namespace lumina\sockets;

use lumina\sockets\events\SocketListener;

abstract class Application extends SocketListener{
	
	protected $path = '';
	protected $request = array();
	protected $fragment = '';
	
	public function getListenerNames(){
		return array('connect', 'disconnect', 'error', 'data');
	}
	
	public function run(){
		
	}
	
	public function onData($event, $data){
		
	}
	
	public function onError($event){
		
	}
	
}