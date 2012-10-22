<?php

namespace lumina\nodes\html\structure;

abstract class Plugin{
	
	private static $currentPath = null;
	private $paths = array();
	private $title = '';
	
	public function __construct($paths, $title){
		if(is_array($paths) && !empty($paths)){
			reset($paths);
			do{
				$p = current($paths);
				$this->paths[] = self::normalizePath($p);
			}while(next($paths) !== false);
		}else{
			$this->paths = array(self::normalizePath($paths));
		}
		$this->title = $title;
	}
	
	private static function normalizePath($path){
		if(trim($path) == "") $path = "/";
		return $path[0] == "/" ? substr($path, 1) : $path;
	} 
	
	public function setTitle($title){
		$this->title = $title;
	}
	
	/**
	 * Gibt den Pfad der Seite zurück
	 * @return string
	 */
	public function getPath(){
		$index = array_search(self::$currentPath, $this->paths);
		return $this->paths[$index];
	}
	
	public static function setCurrentPath($path){
		self::$currentPath = self::normalizePath($path);
	}
	
	public function isPath(){
		return in_array(self::$currentPath, $this->paths);
	}
	
	/**
	 * Gibt den Titel der Seite zurück
	 * @return string
	 */
	public function getTitle(){
		return $this->title;
	}
	
	/**
	 * Hier wird die eigentliche Seite bearbeitet.
	 * Anschließend muss das Resultat im Return
	 * zurückgegeben werden.
	 * @param Node|null $caller Aufrufender Knoten
	 * @return string|Node
	 */
	abstract public function run($caller);
	
}