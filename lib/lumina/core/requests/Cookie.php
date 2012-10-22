<?php

namespace lumina\core\requests;

use lumina\core\Request;

class Cookie extends Request{
	
	const TIME_TEMPORARY = 0;
	const TIME_IMMORTAL = 2147483647;
	
	private $url = null;
	private $path = '/';
	
	/**
	 * @param string $url
	 * @param string $path
	 */
	public function __construct(){
		global $_COOKIE;
		parent::__construct($_COOKIE);
	}
	
	public function defaultCookieContents(){
		$this->setUrl(Config::DEFAULT_COOKIE_URL);
		$this->setPath(Config::DEFAULT_COOKIE_PATH);
	}
	
	/**
	 * @param string $url
	 */
	public function setUrl($url){
		if($url !== null) $this->url = $url;
	}
	
	/**
	 * @param string $path
	 */
	public function setPath($path){
		if($path !== null) $this->path = $path;
	}
	
	/**
	 * @param string $name
	 * @param string $value
	 */
	public function setTemporary($name, $value){
		$this->set($name, $value, self::TIME_TEMPORARY);
	}
	
	/**
	 * @param string $name
	 * @param string $value
	 */
	public function setImmortal($name, $value){
		$this->set($name, $value, self::TIME_IMMORTAL);
	}
	
	/**
	 * @param string $name
	 * @param string $value
	 * @param integer $expiryTime Unix-Timestamp
	 */
	public function set($name, $value, $expiryTime){
		setcookie($name,$value,$expiryTime,$this->path,$this->url);
		$this[$name] = $value;
	}
	
	/**
	 * Löscht Cookie
	 * @param string $name
	 */
	public function remove($name){
		$this->set($name, "", time() - 3600);
		unset($this[$name]);
	}
	
}