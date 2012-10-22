<?php

/**
 * Copyright (c) April 2011 Mario Stöcklein <phoenix4402@gmail.com>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the 
 * Software without restriction, including without limitation the rights to use, copy, 
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, 
 * and to permit persons to whom the Software is furnished to do so, subject to the 
 * following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all 
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */
namespace lumina\lists;

use lumina\core\Object;

class Queue extends Object{
	
	private $items = array();
	
	public function queue(QueueItem $value){
		$this->items[] = $value;
	}
	
	public function shift(){
		return array_shift($this->items);
	}
	
	public function dequeue(){
		$item = array_shift($this->items);
		$item->run($this);
	}
	
	public function isEmpty(){
		return empty($this->items);
	}
	
	public function walk(){
		do{
			$this->dequeue();
		}while(!$this->isEmpty());
	}
	
}