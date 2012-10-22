<?php

namespace lumina\core;

use lumina\core\datatypes\ArrayType;

const STATUS_SUCCESS = 1;
const STATUS_INFO = 2;
const STATUS_WARNING = 3;
const STATUS_ERROR = 4;

/**
 * @method Response singleton()
 * @method Response create()
 */
class Response extends ArrayType{
	
	private $data = array();
	
	/**
	 * @param string $message
	 * @return Response
	 */
	public function setSuccess($message = null){
		if(!$this->isProblem()){
			$this['status'] = STATUS_SUCCESS;
			if(!is_null($message)) $this['msg'] = $message;
		}
		return $this;
	}
	
	/**
	 * @param string $message
	 * @return Response
	 */
	public function setInfo($message){
		if(!$this->isProblem()){
			$this['status'] = STATUS_INFO;
			$this['msg'] = $message;
		}
		return $this;
	}
	
	/**
	 * @param string $name
	 * @param string $message
	 * @return Response
	 */
	public function addWarning($name, $message){
		if(!$this->isError()){
			if(!is_array($this['msg'])) $this['msg'] = new ArrayType();
			$this['status'] = STATUS_WARNING;
			$this['msg'][$name] = $message;
		}
		return $this;
	}
	
	/**
	 * @param string $message
	 * @return Response
	 */
	public function setError($message){
		if(!$this->isError()){
			$this['status'] = STATUS_ERROR;
			$this['msg'] = $message;
		}
		return $this;
	}
	
	public function isError(){
		return isset($this['status']) && $this['status'] === STATUS_ERROR;
	}
	
	public function isWarning(){
		return isset($this['status']) && $this['status'] === STATUS_WARNING;
	}
	
	public function isProblem(){
		return isset($this['status']) && $this['status'] >= STATUS_WARNING;
	}
	
}
