<?php

namespace lumina\core\requests;

use lumina\core\Request;

class File extends Request{
	
	const FILES_NAME = 'name';
	const FILES_TYPE = 'type';
	const FILES_TMP_NAME = 'tmp_name';
	const FILES_ERROR = 'error';
	const FILES_SIZE = 'size';
	
	private function normalize($entry) {
		if(!isset($entry['name']) || !is_array($entry['name']))
			return $entry;
		
		$files = array();
		foreach($entry['name'] as $k => $name) {
			$files[$k] = array(
				'name' => $name,
				'tmp_name' => $entry['tmp_name'][$k],
				'size' => $entry['size'][$k],
				'type' => $entry['type'][$k],
				'error' => $entry['error'][$k]
			);
		}
		return new self($files);
	}
	
	public function current() {
		return $this->normalize(parent::current());
	}
	
	public function offsetGet($offset) {
		return $this->normalize(parent::offsetGet($offset));
	}
	
}