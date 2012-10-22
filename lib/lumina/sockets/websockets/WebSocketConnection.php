<?php

namespace lumina\sockets\websockets;

use lumina\sockets\Application;
use lumina\sockets\events\SocketHandler;
use lumina\sockets\Connection;

/**
 * @method WebSocketConnection singleton()
 * @method WebSocketConnection create()
 * @method string className()
 */
class WebSocketConnection extends Connection{
	
	private static $applications = array();
	
	private $application = null;
	
	public $path = '';
	public $request = array();
	public $fragment = '';
	
	public static function registerApp($path, $appClass){
		$path = parse_url($path, PHP_URL_PATH);
		self::$applications[$path] = $appClass::singleton();
	}
	
	public static function unregisterApp($path){
		$path = parse_url($path, PHP_URL_PATH);
		if(isset(self::$applications[$path])){
			unset(self::$applications[$path]);
		}
	}
	
	private function appLoader(){
		$appPath = substr($this->path, 1);
		if(!isset(self::$applications[$appPath])){
			$this->appTrigger('disconnect', $addition);
			return false;
		}
		$this->application = self::$applications[$appPath];
		return true;
	}
	
	public function getListenerNames(){
		return array('connect', 'disconnect', 'error', 'data', 'flashpolicy');
	}
	
	public function appTrigger($type, array $addition = array()){
		if($this->application){
			$this->trigger($type, array($this->application));
		}

		if((int) $this->socket < 1){
			socket_close($this->socket);
		}
	}
	
	public function onConnect($event){
		$this->appTrigger($event->type, array($this));
	}
	
	public function onDisconnect($event){
		$this->appTrigger($event->type, array($this));
	}
	
	public function onError($event){
		$this->appTrigger($event->type, array($this));
	}
	
	public function send($data){
		// websocket frame
		parent::send(chr(0) . $data . chr(255));
	}
	
	public function handle($data){
		$chunks = explode(chr(255), $data);
		for ($i = 0; $i < count($chunks) - 1; $i++) {
			$chunk = $chunks[$i];
			if (substr($chunk, 0, 1) != chr(0)) {
				$this->appTrigger('error', array(substr($chunk, 1), $this));
				@socket_close($this->socket);
				return false;
			}
			$this->appTrigger('data', array(substr($chunk, 1), $this));
		}
		return true;
	}
	
	public function handshake($data){
		$this->log('Performing handshake');
//		$this->setHandshakeEnabled(true);
		$lines = preg_split("/\r\n/", $data);
		if (count($lines) > 0 && preg_match('/<policy-file-request.*>/', $lines[0])) {
			$this->log('Flash policy file request');
//			$this->log(print_r($lines,true));
			$this->serveFlashPolicy();
			return false;
		}
		
		if (! preg_match('/\AGET (\S+) HTTP\/1.1\z/', $lines[0], $matches)) {
			$this->log('Invalid request: ' . $lines[0]);
			socket_close($this->socket);
			return false;
		}

		$path = $matches[1];
		$this->log($path);

		foreach ($lines as $line) {
			$line = chop($line);
			if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
				$headers[$matches[1]] = $matches[2];
			}
		}
		
		$key3 = '';
		preg_match("#\r\n(.*?)\$#", $data, $match) && $key3 = $match[1];
		
		$origin = $headers['Origin'];
		$host = $headers['Host'];
		
		$this->log($origin);
		$this->log($host);
		
		parse_str(parse_url($path, PHP_URL_QUERY), $this->request);
		$this->fragment = parse_url($path, PHP_URL_FRAGMENT);
		$this->path = parse_url($path, PHP_URL_PATH);
		
		if(!$this->appLoader()){
			$this->log('Invalid application: ' . $path);
			socket_close($this->socket);
			return false;
		}
		
		$status = '101 Web Socket Protocol Handshake';
		if (array_key_exists('Sec-WebSocket-Key1', $headers)) {
			// draft-76
			$def_header = array(
				'Sec-WebSocket-Origin' => $origin,
				'Sec-WebSocket-Location' => "ws://{$host}{$path}"
			);
			$digest = $this->securityDigest($headers['Sec-WebSocket-Key1'], $headers['Sec-WebSocket-Key2'], $key3);
		}
		else if($origin == 'http://localhost'){
			// localhost
			$def_header = array(
				'Sec-WebSocket-Origin' => $origin,
				'Sec-WebSocket-Location' => "ws://{$host}{$path}"
			);
			$digest = '';
		}
		else {
			// draft-75
			$def_header = array(
				'WebSocket-Origin' => $origin,
				'WebSocket-Location' => "ws://{$host}{$path}"  
			);
			$digest = '';
		}
		$header_str = '';
		foreach ($def_header as $key => $value) {
			$header_str .= $key . ': ' . $value . "\r\n";
		}

		$upgrade = "HTTP/1.1 ${status}\r\n" .
				"Upgrade: WebSocket\r\n" .
				"Connection: Upgrade\r\n" .
				"${header_str}\r\n$digest";

		socket_write($this->socket, $upgrade, strlen($upgrade));

		$this->setHandshakeEnabled(true);
		$this->log('Handshake sent');
		
		$this->trigger('connect');
		return true;
	}
	
	private function securityDigest($key1, $key2, $key3){
		return md5(
				pack('N', $this->keyToBytes($key1)) .
				pack('N', $this->keyToBytes($key2)) .
				$key3, true
		);
	}
	
	private function serveFlashPolicy(){
		$policy = '<?xml version="1.0"?>' . "\n";
		$policy .= '<!DOCTYPE cross-domain-policy SYSTEM "http://www.macromedia.com/xml/dtds/cross-domain-policy.dtd">' . "\n";
		$policy .= '<cross-domain-policy>' . "\n";
		$policy .= '<allow-access-from domain="*" to-ports="*"/>' . "\n";
		$policy .= '</cross-domain-policy>' . "\n";
		socket_write($this->socket, $policy, strlen($policy));
		socket_close($this->socket);
//		$this->log('policy sent ' . strlen($policy));
	}
	
	/**
	 * WebSocket draft 76 handshake by Andrea Giammarchi
	 * see http://webreflection.blogspot.com/2010/06/websocket-handshake-76-simplified.html
	 */
	private function keyToBytes($key){
		return preg_match_all('#[0-9]#', $key, $number) && preg_match_all('# #', $key, $space) ?
				implode('', $number[0]) / count($space[0]) : '';
	}
	
}