<?php

namespace lumina\core\requests;

use lumina\core\Request;

/**
 * @method Argument singleton()
 * @method Argument create()
 */
class Argument extends Request{
	
	/**
	 * @var string
	 */
	private $fileExtension = 'html';
		
	public function __construct($str_args = '', $separator = "/"){
		parent::__construct($this->parseArguments($str_args, $separator));
	}
	
	public function __invoke($str_args = null, $separator = "/"){
		if(!is_null($str_args)) $this->parseArguments($str_args, $separator);
	}
	
	public function exchangeArgs($str_args, $separator = "/"){
		$this->fromArray($this->parseArguments($str_args, $separator));
	}
	
	/**
	 * @param string $path
	 */
	private function parseArguments($str_args, $separator){
		if(is_null($str_args)){
			return;
		}
		$args = explode($separator, $str_args);
		$this->extractFileExtension($args);
		$this->removeFileExtension($args);
		return $this->removeEmptyValues($args);
	}
	private function extractFileExtension($args){
		$last = end($args);
		$extension = substr($last, strrpos($last, ".")+1, strlen($last)-1);
		if($extension !== false){
			$this->fileExtension = $extension;
		}
	}
	private function removeFileExtension(&$args){
		$last = array_pop($args);
		$length = strrpos($last, ".");
		if($length !== false) $last = substr($last, 0, $length);
		array_push($args, $last);
	}
	private function removeEmptyValues($args){
		$args = array_filter($args, array($this, 'filterFilledPathValue'));
		return $args;
	}
	private function filterFilledPathValue(&$arg){
		return trim($arg) <> '';
	}
	
	/**
	 * Gibt ein Dateiformat zurück. Sollte das Dateiformat unbekannt sein,
	 * wird "html" zurückgegeben.
	 * @return string
	 */
	public function getExtension(){
		return $this->fileExtension;
	}
	
	public function getController(){
		return $this[0];
	}
	
	public function getAction(){
		return $this[1];
	}
	
	public function offsetGet($offset){
		if(!parent::offsetExists($offset)) return null;
		return parent::offsetGet($offset);
	}
	
}