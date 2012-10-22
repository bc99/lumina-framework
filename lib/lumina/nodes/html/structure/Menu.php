<?php

namespace lumina\nodes\html\structure;

use lumina\nodes\html\Node;

/**
 * @method Menu singleton()
 * @method Menu create()
 * @method string className()
 */
class Menu extends Node{
	
	const DEFAULT_ALIGN_HORIZONTAL = 'lumina-menu-horizontal';
	const DEFAULT_ALIGN_VERTICAL = 'lumina-menu-vertical';
	
	/**
	 * @param string $title
	 * @param string $path
	 */
	public function __construct($path = "/", $title = null, $align = self::DEFAULT_ALIGN_VERTICAL){
		parent::__construct('div');
		$this->addClass('lumina-menu');
		$this->setAlignHorizontalName(self::DEFAULT_ALIGN_HORIZONTAL);
		$this->setAlignVerticalName(self::DEFAULT_ALIGN_VERTICAL);
		$this->title = $title;
		$this->path = parse_url($path, PHP_URL_PATH);
		if(!empty($this->path) && $this->path[0] == "/") $this->path = substr($this->path, 1);
		
		$this->setChildren(Node::create('ul'));
		$this->setAlign($align);
		if(!is_null($title)) $this->setMenuTitle($title);
	}
	
	/**
	 * @param string $title
	 * @return Menu
	 */
	public function setTitle($title){
		if($this->getItem(0)->getTagName() == 'h3'){
			$this->getItem(0)->text($title);
		}
		else{
			$this->prepend(Node::create('h3')->addClass('lumina-default-headline')->text($title));
		}
		return $this;
	}
	
	public function setAlignHorizontalName($name){
		$this->alignH = $name;
	}
	
	public function setAlignVerticalName($name){
		$this->alignV = $name;
	}
	
	public function setAlign($align){
		if(!in_array($align, array($this->alignH, $this->alignV))){
			$align = $this->alignV;
		}
		$iterator = $this->getChildren()->getIterator();
		if($iterator->valid()){
			do{
				$child = $iterator->current();
				if($child->hasClass($align)) break;
				if($child->getTagName() == 'ul'){
					if($align == $this->alignV){
						$child->removeClass($this->alignH)
							->addClass($this->alignV);
					}else{
						$child->removeClass($this->alignV)
							->addClass($this->alignH);
					}
				}
				$iterator->next();
			}while($iterator->valid());
		}
	}
	
	/**
	 * @param string $text
	 * @param string|null $path
	 * @return Menu
	 */
	public function addItem($text, $path = null){
		if($path[0] == "/") $path = substr($path, 1);
		
		$iterator = $this->getChildren()->getIterator();
		if($iterator->valid()){
			do{
				$child = $iterator->current();
				if($child->getTagName() == 'ul'){
					if($this->path == $path){
						$child->append(Node::create('li')->addClass('lumina-item-active')->text($text));
					}
					else if($path === null){
						$child->append(Node::create('li')->html(Node::create('strong')->text($text)));
					}
					else{
						$child->append(Node::create('li')->html(Node::create('a')->setAttr('href', "/$path")->text($text)));
					}
				}
				$iterator->next();
			}while($iterator->valid());
		}
		return $this;
	}
}

