<?php

namespace lumina\physics\units;

use lumina\physics\Unit;

class Byte extends Unit{
	
	const BYTE = 1;
	const KBYTE = 1024;
	const MBYTE = 1048576;
	const GBYTE = 1073741824;
	const TBYTE = 1099511627776;
	
	public function getDefaultType(){
		return self::BYTE;
	}
	
}