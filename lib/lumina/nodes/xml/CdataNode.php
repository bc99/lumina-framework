<?php

namespace lumina\nodes\xml;

class CdataNode extends \lumina\nodes\Node{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function render(){
		$out .= "<![CDATA[\n{$this->children->render()}\n]]>";
		return $out;
	}
	
}