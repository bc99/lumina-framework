<?php

namespace lumina\nodes\html\boxes;

use lumina\nodes\html\Node;
use lumina\nodes\html\Option;

/**
 * @method Column singleton()
 * @method Column create()
 */
class Column extends Node{
	
	const ALIGN_LEFT = 'left';
	const ALIGN_RIGHT = 'right';
	const ALIGN_JUSTIFY = 'justify';

	private $columns = array();
	
	public function __construct(){
		parent::__construct('div');
		$this->addClass('lumina-layout');
		$this->setOptionEntry('align', array(
			self::ALIGN_LEFT => 'lumina-column-left',
			self::ALIGN_RIGHT => 'lumina-column-right',
			self::ALIGN_JUSTIFY => 'lumina-column-main',
		));
	}
	
	/**
	 * Erzeugt die rechte Spalte eines Weblayouts. Eine feste
	 * Breite ist vorausgesetzt.
	 * @param string $align
	 * @param int|string $width
	 * @param NodeList|array $nodeList
	 * @return Node
	 */
	private function createColumn($align, $width, $nodeList = array()){
		$classes = $this->getOptionValue('align');
		$this->columns[$align] = $this->newTag('div')
			->addClass($classes[$align] . ' clearfix')
			->append($this->newTag('div')
				->addClass('lumina-content clearfix')
				->setChildren($nodeList)
			);
		$this->changeColumnWidth($align, $width);
	}
	
	private function changeColumnWidth($align, $width){
		if(!is_null($width)){
			$this->columns[$align]->setAttr('style', "width: {$width};")
				->setOptionEntry('width', $width);
		}else{
			$this->columns[$align]->removeAttr('style')
				->setOptionEntry('width', null);
		}
	}
	
	/**
	 * Erstellt/bearbeitet die linke Spalte eines Weblayouts.
	 * @param int|string $width
	 * @param NodeList|array $nodeList
	 * @return Column
	 */
	public function setColumnLeft($width, $nodeList = array()){
		if(!isset($this->columns[self::ALIGN_LEFT]))
			$this->createColumn(self::ALIGN_LEFT, $width, $nodeList);
		else {
			$this->changeColumnWidth(self::ALIGN_LEFT, $width);
			$this->columns[self::ALIGN_LEFT]->getItem(0)->setChildren($nodeList);
		}
		return $this;
	}
	
	/**
	 * Gibt die linke Spalte des Weblayouts zurück. Falls keine
	 * linke Spalte existieren sollte, wird NULL zurück gegeben.
	 * @return Node
	 */
	public function getColumnLeft(){
		if(!isset($this->columns[self::ALIGN_LEFT])) return null;
		return $this->columns[self::ALIGN_LEFT];
	}
	
	/**
	 * Erstellt/bearbeitet die rechte Spalte eines Weblayouts.
	 * @param int|string $width
	 * @param NodeList|array $nodeList
	 * @return Column
	 */
	public function setColumnRight($width, $nodeList = array()){
		if(!isset($this->columns[self::ALIGN_RIGHT]))
			$this->createColumn(self::ALIGN_RIGHT, $width, $nodeList);
		else {
			$this->changeColumnWidth(self::ALIGN_RIGHT, $width);
			$this->columns[self::ALIGN_RIGHT]->getItem(0)->setChildren($nodeList);
		}
		return $this;
	}
	
	/**
	 * Gibt die linke Spalte des Weblayouts zurück. Falls keine
	 * linke Spalte existieren sollte, wird NULL zurück gegeben.
	 * 
	 * @return Node|null
	 */
	public function getColumnRight(){
		if(!isset($this->columns[self::ALIGN_RIGHT])) return null;
		return $this->columns[self::ALIGN_RIGHT];
	}
	
	/**
	 * Erstellt/bearbeitet das Hauptfenster des Weblayouts.
	 * @param NodeList|array $nodeList
	 * @return Column
	 */
	public function setColumnMain($nodeList = array()){
		if(!isset($this->columns[self::ALIGN_JUSTIFY])){
			$this->createColumn(self::ALIGN_JUSTIFY, null, $nodeList);
		} 
		else $this->columns[self::ALIGN_JUSTIFY]->getItem(0)->setChildren($nodeList);
		return $this;
	}
	
	/**
	 * Gibt die linke Spalte des Weblayouts zurück. Falls keine
	 * linke Spalte existieren sollte, wird NULL zurück gegeben.
	 * @return Node
	 */
	public function getColumnMain(){
		if(!isset($this->columns[self::ALIGN_JUSTIFY])) return null;
		return $this->columns[self::ALIGN_JUSTIFY];
	}
	
	/**
	 * @param string $tagName
	 * @return Node
	 */
	public function newTag($tagName){
		return Node::create($tagName, $this->option->isXhtml());
	}
	
	public function render(){
		$columns = array();
		$cssMargin = null;
		
		if(isset($this->columns[self::ALIGN_LEFT])){
			$columns[] = $this->columns[self::ALIGN_LEFT];
			$cssMargin = "margin-left: {$this->columns[self::ALIGN_LEFT]->getOptionValue('width')};";
		}
		
		if(isset($this->columns[self::ALIGN_RIGHT])){
			$columns[] = $this->columns[self::ALIGN_RIGHT];
			$cssMargin .= "margin-right: {$this->columns[self::ALIGN_RIGHT]->getOptionValue('width')};";
		}
		
		if(isset($this->columns[self::ALIGN_JUSTIFY])){
			$columns[] = $this->columns[self::ALIGN_JUSTIFY];
			if(!is_null($cssMargin))
				$this->columns[self::ALIGN_JUSTIFY]->setAttr('style', $cssMargin);
		}
		
		$this->setChildren($columns);
		return parent::render() . '<!--[IF IE]><div class="ieclear">&nbsp;</div><![ENDIF]-->';
	}
	
}