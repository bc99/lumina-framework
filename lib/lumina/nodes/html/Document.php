<?php

namespace lumina\nodes\html;

use lumina\core\Object;
use lumina\nodes\html\Node;
use lumina\nodes\html\Option;
use lumina\nodes\html\boxes\Column;

const XML_CONFORM_DISABLED = false;
const XML_CONFORM_ENABLED = true;

/**
 * @method Document create()
 * @method Document singleton()
 * @method string className()
 */
class Document extends Node{

	private $html = array();
	
	/**
	 * 
	 * Enter description here ...
	 * @param string $title
	 * 		Title of the document. It will be shown in the title tag
	 * @param Option $option
	 * @param boolean $isXhtml
	 */
	public function __construct($title = "", Option $option = null){
		if(!is_null($option)) $this->setOption($option);
		$this->option->setTagName('html');
		$this->option->set('title', $title);
		
		$doctype = $this->option->getDoctype();
		
		// HTML-Elements (no xml-syntax)
		if(in_array($doctype, array(
				Node::HTML_4_STRICT, Node::HTML_4_TRANS,
				Node::HTML_4_FRAME, Node::HTML_5))){
			$this->option->setXhtmlEnabled(Node::XHTML_DISABLED);
		}
		
		// XHTML-Elements (with xml-syntax)
		else if(in_array($doctype, array(
				Node::XHTML_10_FRAME, Node::XHTML_10_STRICT,
				Node::XHTML_10_TRANS, Node::XHTML_11))){
			$this->option->setXhtmlEnabled(Node::XHTML_ENABLED);
		}
		
		// Otherwise the user knows what he is doing when
		// he implement his own document type ;-)
		
		if($this->option->isXhtml()){
			$contentType = "application/xhtml+xml";
			$this->setAttr("xml:lang", "en");
			$this->setAttr("xmlns", "http://www.w3.org/1999/xhtml");
		}else{
			$contentType = "text/html";
		}
		
		$this->setOptionEntry('content-type', $contentType);
		
		$this->html['head'] = $this->newTag("head")
			->append($this->newTag("title")->text($this->option->get('title')))
			->append($this->newTag("meta")->setAttr('http-equiv', "content-type")->setAttr('content', "$contentType; charset={$this->option->getEncoding()}"));
		$this->html['page'] = array(
			'header' => $this->newTag("div")->setAttr('id', "page-head"),
			'content' => $this->newTag("div")->setAttr('id', "page-content"),
			'footer' => $this->newTag("div")->setAttr('id', "page-foot"),
		);
		$this->html['body'] = $this->newTag("body")->append(
			$this->newTag("div")->setAttr('id', "outer-wrapper")->append(
				$this->newTag("div")
					->setAttr('id', "inner-wrapper")
					->setChildren(array_values($this->html['page']))
			)
		);
		parent::append($this->html['head']);
		parent::append($this->html['body']);
	}
	
	public function render(){
		$head = $this->getHead();
		
//		$start = microtime(true);
		
		$js = $this->option->get('jspaths', true);
		if(!empty($js)){
			reset($js);
			do{
				$path = current($js);
				$head->append('<script src="' . htmlentities($path, ENT_COMPAT, $this->option->getEncoding()) . '" type="text/javascript"></script>');
			}while(next($js) !== false);
		}
		
		$css = $this->option->get('csspaths', true);
		if(!empty($css)){
			do{
				list($path, $media, $force_ie) = current($css);
				$link = '<link media="' .
					htmlentities($media, ENT_COMPAT, $this->option->getEncoding()) . 
					'" href="' . 
					htmlentities($path, ENT_COMPAT, $this->option->getEncoding()) . 
					'" type="text/css" rel="stylesheet"' .
					($this->option->isXhtml()?'/':'') . 
				'>';
				if($force_ie !== false){
					$head->append("<!--[IF IE]>{$link}<![ENDIF]-->");
				}else{
					$head->append($link);
				}
			}while(next($css) !== false);
		}
		
		if($this->option->isXhtml()){
			$xmlheader = "<?xml version=\"1.0\" encoding=\"{$this->option->getEncoding()}\"?>";
		}else{
			$xmlheader = "";
		}
		$doctype = $this->option->getDoctype();
		$out = $xmlheader . $doctype . parent::render();
		
//		$end = microtime(true);
//		
//		echo "<pre>";
//		echo "Render-Time: " . (($end - $start) * 1000) . "µsec";
//		echo "</pre>";
		
		return $out;
	}
	
	/**
	 * @param string $path path or url
	 * @return Document
	 */
	public function addJsPath($path){
		$jspaths = $this->getOptionValue('jspaths');
		if(empty($jspaths)) $jspaths = array();
		$jspaths[] = $path;
		$this->setOptionEntry('jspaths', array_unique(array_filter($jspaths)));
		return $this;
	}
	
	public function getTitle(){
		return $this->option->get('title', true);
	}
	
	/**
	 * @param string $path path or url
	 * @param string $media screen, projector, print, etc.
	 * @param boolean $force_ie force this style for internet explorer
	 * @return Document
	 */
	public function addCssPath($path, $media = 'all', $force_ie = false){
		$csspaths = $this->getOptionValue('csspaths');
		if(empty($csspaths)) $csspaths = array();
		$csspaths[] = array($path, $media, $force_ie);
		$this->setOptionEntry('csspaths', $csspaths);
		return $this;
	}
	
	/**
	 * @return Node
	 */
	public function getHead(){
		return $this->html['head'];
	}
	
	/**
	 * @return Node
	 */
	public function getBody(){
		return $this->html['body'];
	}
	
	/**
	 * @return Node
	 */
	public function getPageHead(){
		return $this->html['page']['header'];
	}
	
	/**
	 * @return Node
	 */
	public function getPageContent(){
		return $this->html['page']['content'];
	}
	
	/**
	 * @return Node
	 */
	public function getPageFoot(){
		return $this->html['page']['footer'];
	}
	
	/**
	 * Das hier erzeugte Node-Objekt beinhaltet u.A.
	 * die Möglichkeit zum Dokument zurück zu kehren.
	 * @param string $tagName
	 * @return Node
	 */
	public function newTag($tagName){
		return Node::create($tagName, $this->option->isXhtml())->setOptionEntry('document', $this);
	}
	
	/**
	 * @param string $tagName
	 * @return Column
	 */
	public function newLayoutColumn(){
		return Column::create();
	}
	
}
