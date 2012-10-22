<?php

namespace lumina\physics\units;

use lumina\physics\Unit;

class Time extends Unit{
	
	const SECOND = 1;
	const MINUTE = 60;
	const HOUR   = 3600;
	const DAY    = 86400;
	const YEAR   = 31557600;
	
	public function getDefaultType(){
		return self::SECOND;
	}
	
}