<?php

namespace lumina\core;

use lumina\core\Request;
use lumina\core\requests\Cookie;
use lumina\core\requests\File;
use lumina\core\requests\Argument;
use lumina\core\requests\Server;

/**
 * @property Argument $args
 * @property Server $server
 * @property Request $request
 * @property Cookie $cookie
 * @property File $file
 */
abstract class Controller extends Object{
	
	public $controllerName;
	public $actionName;
	private $defaultAction;
	
	public function __construct($defaultAction = 'index'){
		$this->defaultAction = $defaultAction;
		$this->initAction();
	}
	
	public function __get($name){
		switch($name){
			// TODO Diese zwei Getter müssen in allen Projekten angepasst werden.
			// Danach können sie gelöscht werden.
			case 'controller': return $this->controllerName;
			case 'action': return $this->actionName;
			
			// Diese Getter müssen erhalten bleiben
			case 'args': return Argument::singleton();
			case 'server': return Server::singleton();
			case 'cookie': return Cookie::singleton();
			case 'files': return File::singleton($_FILES);
			case 'request': return Request::singleton($_GET, $_POST);
		}
	}
	
	public function __set($name, $value){
		switch($name){
			// TODO Diese zwei Getter müssen in allen Projekten angepasst werden.
			// Danach können sie gelöscht werden.
			case 'controller': $this->controllerName = $value; break;
			case 'action': $this->actionName = $value; break;
		}
	}
	
	public function getPath($actionName = null){
		return "/{$this->controllerName}/".(
			method_exists($this, $actionName)
				? $actionName
				: $this->defaultAction
		);
	}
	
	protected function getDefaultAction(){
		return $this->defaultAction;
	}
	
	protected function useDefaultAction(){
		$this->actionName = $this->defaultAction;
	}
	
	protected function initAction(){
		$this->controller = $this->args->getController();
		$actionName = $this->args->getAction();
		if($actionName !== false && method_exists($this, $actionName))
			$this->actionName = $actionName;
		else
			$this->actionName = $this->defaultAction;
	}
	
	final public function loadAction(){
		if(empty($this->actionName)){
			return false;
		}else{
			$this->ensureActionSyntaxIsValid();
			$this->ensureActionExists();
		}
		$this->{$this->actionName}();
	}
	
	private function ensureActionSyntaxIsValid(){
		if(!preg_match('/^\w[\w\d\_]+$/',$this->actionName)){
			throw new \Exception('Die Action "' .$this->controllerName. '/' .$this->actionName. '" ist ungültig.', E_USER_ERROR);
		}
	}
	
	private function ensureActionExists(){
		if(!method_exists($this,$this->actionName)){
			throw new \Exception('Die Action "'.$this->controllerName.'/' . $this->actionName. '" existiert nicht.', E_USER_ERROR);
		}
	}
	
	abstract public function run();
	
}