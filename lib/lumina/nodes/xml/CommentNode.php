<?php

namespace lumina\nodes\xml;

class CommentNode extends \lumina\nodes\Node{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function render(){
		$out .= "<!--{$this->children->render()}-->";
		return $out;
	}
	
}