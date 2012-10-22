<?php

namespace lumina\nodes\html\structure;

use lumina\nodes\html\Node;

/**
 * @method Page create()
 * @method Page singleton()
 * @method string className()
 */
class Page extends Node{
	
	/**
	 * @var Menu
	 */
	private $menu = null;
	private $path = null;
	
	public function __construct($path = "/", $defaultPath = 'index.html'){
		parent::__construct('div');
		$this->addClass('lumina-page');
		$this->path = parse_url($path, PHP_URL_PATH);
		if(!empty($this->path) && $this->path[0] == "/") $this->path = substr($this->path, 1);
		if(empty($this->path)) $this->path = $defaultPath;
		
		$this->setChildren(Node::create('div')->addClass("lumina-page-content"));
		$this->menu = Menu::create($path);
	}
	
	public function setDefaultPath($defaultPath){
		return $this;
	}
	
	/**
	 * @return Menu
	 */
	public function getMenu(){
		return $this->menu;
	}
	
	/**
	 * @param Plugin $plugin
	 * @return Page 
	 */
	public function addPlugin($plugin, $standalone = false){
		if(!$standalone) $this->menu->addItem($plugin->getTitle(), $plugin->getPath(0));
		if($plugin->isPath($this->path)){
			$this->getItem(0)->append($plugin->run($this));
			$this->prepend(Node::create('h3')->addClass('lumina-default-headline')->text($plugin->getTitle()));
		}
		return $this;
	}
	
	/**
	 * @param string $text
	 * @param string $url
	 * @return Page
	 */
	public function addItem($title, $path, $callback, $standalone = false){
		if(!empty($this->path) && $path[0] == "/") $path = substr($path, 1);
		
		if(!$standalone) $this->menu->addItem($title, $path);
		if($this->path == $path){
			$this->getItem(0)->append($callback($this));
			$this->prepend(Node::create('h3')->addClass('lumina-default-headline')->text($title));
		}
		return $this;
	}
	
}

