<?php

namespace lumina\core\requests;

use lumina\core\Request;

/**
 * @method Server singleton()
 * @method Server create()
 */
class Server extends Request{
	
	/**
	 * @global array $_SERVER
	 */
	public function __construct(){
		parent::__construct($_SERVER);
	}
	
	public function getIp($alternative = null){
		// share internet
		if($this->HTTP_CLIENT_IP(false) && $this->checkIfIp($this['HTTP_CLIENT_IP']))
			return $this['HTTP_CLIENT_IP'];
		// proxy
		if($this->HTTP_X_FORWARDED_FOR(false) && $this->checkIfIp($this['HTTP_X_FORWARDED_FOR']))
			return $this['HTTP_X_FORWARDED_FOR'];
		// default
		if($this->REMOTE_ADDR(false) && $this->checkIfIp($this['REMOTE_ADDR']))
			return $this['REMOTE_ADDR'];
		// no chance
		return $alternative;
	}
	
	private function checkIfIp($ip){
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 & FILTER_FLAG_IPV6) == $ip;
	}
	
}