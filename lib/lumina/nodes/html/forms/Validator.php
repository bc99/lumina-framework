<?php

namespace lumina\nodes\html\forms;

use lumina\core\Object;
use lumina\core\Response;

use lumina\nodes\html\Option;

abstract class Validator extends Object{
	
	/**
	 * @var Response
	 */
	private $response = null;
	
	/**
	 * @var Element
	 */
	protected $element = null;
	
	/**
	 * @var Option
	 */
	private $option = null;
	
	public function __construct(){
		$this->response = Response::create();
		$this->option = Option::create();
		$this->init();
	}
	
	protected function setError($msg){
		$this->response->setError($msg);
	}
	
	protected function isError(){
		return $this->response->isProblem();
	}
	
	protected function addWarning($name, $msg){
		$this->response->addWarning($name, $msg);
	}
	
	protected function setOption($name, $value){
		$this->option->set($name, $value);
	}
	
	protected function getOption($name){
		return $this->option->get($name, true);
	}
	
	/**
	 * @param Element $element
	 * @param mixed $input
	 * @return boolean
	 */
	public function isValid($element, $value){
		$element->setValue($value);
		$this->doValidate($element, $value);
		return !$this->isError();
	}
	
	public function getResponseArray(){
		return $this->response->toArray();
	}
	
	public function getResponse(){
		return $this->response;
	}
	
	protected function init(){
		
	}
	
	/**
	 * @param Element $element
	 * @param mixed $input
	 */
	abstract protected function doValidate($element, $value);
	
}
