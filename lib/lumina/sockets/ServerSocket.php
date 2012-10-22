<?php

namespace lumina\sockets;

use lumina\forks\Fork;

use lumina\sockets\events\SocketHandler;
use lumina\sockets\events\SocketListener;

use lumina\sockets\websockets\WebSocketConnection;

/**
 * @method ServerSocket create()
 * @method ServerSocket singleton()
 */
class ServerSocket extends SocketListener{
	
	const SIGNAL_START = 1;
	const SIGNAL_STOP = 0;
	
	private $signal = 0;
	private $connClass = null;
	
	/**
	 * @var Fork
	 */
	private $fork = null;
	
	/**
	 * @var SocketHandler
	 */
	private $handler = null;
	
	public function __construct($host = '127.0.0.1', $port = 2222, $connectionClass = null){
		if(!is_object($connectionClass) && class_exists($connectionClass)){
			$this->connClass = $connectionClass;
		}
		else if($connectionClass instanceof Connection){
			$this->connClass = $connectionClass::className();
		}
		else{
			$this->connClass = WebSocketConnection::className();
		}
		$this->handler = SocketHandler::singleton($this)->createSocket($host, $port);
		$this->handler->addListenerObject($this);
//		$this->fork = new Fork($this);
	}
	
	public function startServer(){
		$this->signal = self::SIGNAL_START;
//		$this->fork->start();
		$this->run();
	}
	
	public function stopServer(){
		$this->signal = self::SIGNAL_STOP;
		// TODO Fork muss das Signal auch erhalten.
	}
	
	public function onStart($event){
		echo "Der Server wird gestartet.\n";
	}
	
	public function onStop($event){
		echo "Der Server wird heruntergefahren.\n";
	}
	
	public function onConnect($event){
		echo "Ein neuer Client hat sich verbunden.\n";
	}
	
	public function onDisconnect($event){
		echo "Ein Client hat die Verbindung getrennt.\n";
	}
	
	public function onError($event){
		echo "Ein Fehler ist aufgetreten.\n";
	}
	
	public function getListenerNames(){
		return array('start', 'stop', 'connect', 'disconnect', 'error');
	}
	
	public function run(){
		$this->handler->trigger('start', $this);
		$connClass = $this->connClass;
		do{
			$changed_sockets = $this->handler->getSockets();
			@socket_select($changed_sockets, $write = null, $exceptions = null, null);
			
			foreach($changed_sockets as $socket){
				if($this->handler->isMaster($socket)){
					$resource = $this->handler->accept();
					if($resource >= 0){
						$client = $connClass::create($this, $resource);
						$this->handler->addListenerObject($client);
						$this->handler->addSocket($resource);
					}
					else{
						echo 'Socket error: ' . socket_strerror(socket_last_error($resource)) . "\n";
					}
				}
				else{
					$client = $connClass::getConnection($socket);
					$bytes = @socket_recv($socket, $data, 4096, 0);
					if($bytes === 0){
						$this->handler->trigger('disconnect', $client);
						$this->handler->removeSocket($socket);
						$connClass::removeConnection($socket);
						unset($client);
					}
					else{
						if(!$client->isHandshaked()){
							$client->handshake($data);
						}else{
							$client->handle($data);
						}
					}
				}
			}
			if(!$this->signal) $this->handler->trigger('stop', $this);
		}while($this->signal);
	}
	
	public function log($socket, $msg){
		if(@socket_getpeername($socket, $addr, $port)){
			echo '[client ' . $addr . ':' . $port . '] ' . $msg . "\n";
		}
		else{
			echo '[client] ' . $msg . "\n";
		}
	}
	
}