<?php

namespace lumina\physics\units;

use lumina\physics\Unit;

class Distance extends Unit{
	
	const NANOMETER  = 0.000000001;
	const MIKROMETER = 0.000001;
	const MILLIMETER = 0.001;
	const CENTIMETER = 0.01;
	const DEZIMETER  = 0.1;
	const METER      = 1;
	const DEKAMETER  = 10;
	const HEKTOMETER = 100;
	const KILOMETER  = 1000;
	
	public function getDefaultType(){
		return self::METER;
	}
	
}