<?php

namespace lumina\nodes\html\forms\validators;

use lumina\nodes\html\forms\Form;
use lumina\nodes\html\forms\Validator;

const TL_LENGTH = 0;
const TL_MESSAGE = 1;

/**
 * @method TextLength singleton()
 * @method TextLength create()
 */
class TextLength extends Validator{
	
	public function init(){
		$this->setMin(0, "Kein Text vorhanden.");
		$this->setMax(100, "Höchstens %d Zeichen.");
	}
	
	/**
	 * @param integer $min
	 * @param string|null $errorMsg
	 * @return TextLength
	 */
	public function setMin($min, $errorMsg = null){
		$opt = $this->getOption('min', true);
		$opt = is_array($opt) ? $opt : array();
		
		$opt[TL_LENGTH] = $min;
		if(!is_null($errorMsg)){
			$opt[TL_MESSAGE] = $errorMsg;
		}
		
		$this->setOption('min', $opt);
		return $this;
	}
	
	public function getMinLength(){
		$min = $this->getOption('min');
		return $min[TL_LENGTH];
	}
	
	public function getMinMsg(){
		$min = $this->getOption('min');
		return $min[TL_MESSAGE];
	}
	
	/**
	 * @param integer $max
	 * @param string|null $errorMsg
	 * @return TextLength
	 */
	public function setMax($max, $errorMsg = null){
		$opt = $this->getOption('max', true);
		$opt = is_array($opt) ? $opt : array();
		
		$opt[TL_LENGTH] = $max;
		if(!is_null($errorMsg)){
			$opt[TL_MESSAGE] = $errorMsg;
		}
		
		$this->setOption('max', $opt);
		return $this;
	}
	
	public function getMaxLength(){
		$max = $this->getOption('max');
		return $max[TL_LENGTH];
	}
	
	public function getMaxMsg(){
		$max = $this->getOption('max');
		return $max[TL_MESSAGE];
	}
	
	protected function doValidate($element, $value){
		$length = mb_strlen(trim($value));
		if($length < $this->getMinLength()){
			$this->addWarning($element->getAttr('name'), sprintf($this->getMinMsg(), $this->getMinLength()));
		}else if($length > $this->getMaxLength()){
			$this->addWarning($element->getAttr('name'), sprintf($this->getMaxMsg(), $this->getMaxLength()));
		}
		return !$this->isError();
	}
	
}
