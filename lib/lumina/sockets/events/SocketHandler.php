<?php

namespace lumina\sockets\events;

use lumina\sockets\events\SocketFork;

use lumina\forks\Runnable;

use lumina\events\EventHandler;

/**
 * @method SocketHandler create()
 * @method SocketHandler singleton()
 * @method SocketHandler className()
 */
class SocketHandler extends EventHandler{
	
	private $fork = null;
	
	private $master = null;
	private $allsockets = array();
	
	public function __construct($server = null){
		parent::__construct($server);
	}
	
	/**
	 * Create a socket on given host/port
	 * 
	 * @param string $host The host/bind address to use
	 * @param int $port The actual port to bind on
	 * @return SocketHandler
	 */
	public function createSocket($host, $port){
		if(($this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
			die("socket_create() failed, reason: " . socket_strerror($this->master)."\n");
		}
		
		echo "Socket {$this->master} created.";
		
		socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1);
		#socket_set_option($master,SOL_SOCKET,SO_KEEPALIVE,1);
		
//		var_dump($host, $port);
		
		if (($ret = socket_bind($this->master, $host, $port)) < 0) {
			die("socket_bind() failed, reason: " . socket_strerror($ret)."\n");
		}
		
		echo "Socket bound to {$host}:{$port}.\n";
		
		if (($ret = socket_listen($this->master, 5)) < 0) {
			die("socket_listen() failed, reason: " . socket_strerror($ret)."\n");
		}
		
		echo 'Start listening on Socket.'."\n";
		
		$this->allsockets[] = $this->master;
		return $this;
	}
	
	public function trigger($type, $element, $addition = array(), $eventClass = null){
		if($element instanceof Runnable){
			if(is_null($eventClass)) $eventClass = SocketEvent::className();
			
			$callableTypes = $element->getListenerNames();
			foreach(array_intersect($callableTypes, explode(' ', $type)) as $t){
				// Initialisieren des Events. Es kann ein über
				// den Konstruktor individuell definiertes Event
				// sein, oder ein Standard-Event.
				$event = new SocketEvent($t, $this->root, $element, $addition);
				$parameters = array_merge(array($event), $addition);
				
				// Erzeuge ein Kindprozess und feuer das Event
//				$fork = new SocketFork($element, $t, $parameters);
//				$fork->start();
			}
			
			// Warte, bis alle Kindprozesse fertig sind.
//			SocketFork::waitForProcess();
		}
	}
	
	public function accept(){
		return socket_accept($this->master);
	}
	
	public function isMaster($socket){
		return $this->master == $socket;
	}
	
	public function addSocket($socket){
		if(!array_search($socket, $this->allsockets, true)){
			$this->allsockets[] = $socket;
		}
	}
	
	public function removeSocket($socket){
		if($index = array_search($socket, $this->allsockets, true)){
			unset($this->allsockets[$index]);
			$this->allsockets = array_values($this->allsockets);
		}
	}
	
	public function getSockets(){
		return $this->allsockets;
	}
	
}