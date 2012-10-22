<?php

namespace lumina\sockets;

use lumina\sockets\events\SocketHandler;

use lumina\sockets\ServerSocket;

use lumina\forks\Runnable;

use lumina\sockets\events\SocketListener;

/**
 * @method Connection singleton()
 * @method Connection create()
 * @method string className()
 */
abstract class Connection extends SocketListener{
	
	private static $connections = array();
	private $handshaked = false;
	
	/**
	 * @var ServerSocket
	 */
	protected $server;
	protected $socket;
	
	/**
	 * @param resource $socket
	 * @return Connection|false
	 */
	public static function getConnection($socket){
		$connection = isset(self::$connections[static::className()][(int) $socket])
				? self::$connections[static::className()][(int) $socket] : false;
		return $connection;
	}
	
	public static function removeConnection($socket){
		if(isset(self::$connections[static::className()][(int) $socket])){
			unset(self::$connections[static::className()][(int) $socket]);
		}
	}
	
	public function __construct($server, $socket){
		$this->server = $server;
		$this->socket = $socket;
		if(!isset(self::$connections[static::className()])){
			self::$connections[static::className()] = array();
		}
		if(isset(self::$connections[static::className()][(int) $socket])){
			$this->log("socket $socket already exists!");
			@socket_close($socket);
		}
		else{
			self::$connections[static::className()][(int) $socket] = $this;
		}
	}
	
	public function getListenerNames(){
		return array('connect', 'disconnect', 'error', 'data');
	}
	
	public function trigger($type, array $addition = array(), $eventClass = null){
		SocketHandler::singleton()->trigger($type, $this, $addition, $eventClass);
	}
	
	public function send($data){
		if(!@socket_write($this->socket, $data, strlen($data))){
			@socket_close($this->socket);
			$this->socket = false;
		}
	}
	
	public function isHandshaked(){
		return $this->handshaked;
	}
	
	protected function setHandshakeEnabled($enable){
		$this->handshaked = (boolean) $enable;
	}
	
	abstract public function handle($data);
	abstract public function handshake($data);
	public function run(){}
	

	public function log($msg){
		if(@socket_getpeername($this->socket, $addr, $port)){
			echo '[client ' . $addr . ':' . $port . '] ' . $msg . "\n";
		}
		else{
			echo '[client] ' . $msg . "\n";
		}
	}
}