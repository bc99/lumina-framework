<?php

namespace lumina\core\datatypes;
use lumina\core\Object;

/**
 * @method String singleton()
 * @method String create()
 */
class StringType extends Object{
	
	/**
	 * @var string
	 */
	private $encoding;
	
	/**
	 * @var string
	 */
	private $str = '';
	
	/**
	 * @var string
	 */
	private $original = '';
	
	/**
	 * Initialisiert das StringType. Es kann ein String übergeben
	 * werden.
	 * @param string $str
	 */
	public function __construct($str = '', $encoding = "UTF-8"){
		$this->set($str);
		$this->setEncoding($encoding);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lumina\core.Object::__invoke()
	 */
	public function __invoke($params){
		$this->set($params[0]);
		if(isset($params[1])) $this->setEncoding($params[1]);
		return $this;
	}
	
	/**
	 * Gibt die veränderte Zeichenkette zurück
	 * @return string
	 */
	public function __toString(){
		return $this->get();
	}
	
	/**
	 * Gibt die veränderte Zeichenkette zurück
	 * @return string
	 */
	public function toString(){
		return (string) $this;
	}
	
	public function setEncoding($encoding){
		$this->encoding = $encoding;
	}
	
	/**
	 * Übergabe der zu bearbeitenden Zeichenkette
	 * @param string $str
	 * @return StringType
	 */
	public function set($str){
		$this->str = $this->original = (string) $str;
		return $this;
	}
	
	/**
	 * Gibt die veränderte Zeichenkette zurück
	 * @return string
	 */
	public function get(){
		return $this->str;
	}
	
	/**
	 * Gibt die Original-Zeichenkette zurück
	 * @return string
	 */
	public function getOriginal(){
		return $this->original;
	}
	
	/**
	 * Entfernt Whitespaces am Anfang und Ende
	 * @return StringType
	 */
	public function trim(){
		$this->str = trim($this->str);
		return $this;
	}
	
	/**
	 * Beschneidet ab der Position $pos und setzt einen Suffix
	 * @param int $pos
	 * @param string $suffix
	 * @return StringType
	 */
	public function cut($pos, $suffix = '...'){
		$this->str = mb_strlen($this->str) > $pos
				? mb_substr($this->str, 0, $pos - mb_strlen($suffix), $this->encoding) . $suffix : $this->str;
		return $this;
	}
	
	/**
	 * Entfernt HTML-Tags
	 * @return StringType
	 */
	public function removeTags(){
		$this->str = strip_tags($this->str);
		return $this;
	}
	
	/**
	 * @param string $replacement
	 * @return StringType
	 */
	public function removeScript($replacement = '### script ###'){
		$this->str = preg_replace('/(<script.*?>.*?<\/script>)/i',$replacement, $this->str);
		$this->str = preg_replace('/(<.*?(onclick|onmouseover|onmouseout|onload|onkeyup|onkeydown|onmouseup|onmousedown|onfocus|onblur).*?>)/i',$replacement, $this->str);
		return $this;
	}
	
	/**
	 * Wandelt Sonderzeichen in HTML-Entitäten um.
	 * @param integer $quote_style @see htmlentities
	 * @return StringType
	 */
	public function htmlEntity($quote_style = null){
		$this->str = htmlentities($this->str, $quote_style, $this->encoding);
		return $this;
	}
	
	/**
	 * @return StringType
	 */
	public function toLower(){
		$this->str = mb_strtolower($this->str, $this->encoding);
		return $this;
	}
	
	/**
	 * @return StringType
	 */
	public function toUpper(){
		$this->str = mb_strtoupper($this->str, $this->encoding);
		return $this;
	}
	
}