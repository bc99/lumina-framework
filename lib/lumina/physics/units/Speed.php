<?php

namespace lumina\physics\units;

use lumina\physics\Number;

use lumina\physics\units\Time;
use lumina\physics\units\Distance;

class Speed extends Number{
	
	private $distanceType;
	private $timeType;
	
	public function __construct($number, $distanceType = Distance::METER, $timeType = Time::SECOND){
		$this->distanceType = $distanceType;
		$this->timeType = $timeType;
		parent::__construct($this->calculateSpeed($number));
	}
	
	private function calculateOrigSpeed(){
		return $this->get()*($this->distanceType/$this->timeType);
	}
	
	private function calculateSpeed($number){
		return $number*($this->timeType/$this->distanceType);
	}
	
	private function calculateForDistance(Time $time){
		return $time->get()*$this->calculateOrigSpeed()*($time->getType()/$this->distanceType);
	}
	
	private function calculateForTime(Distance $distance){
		return $distance->get()/$this->calculateOrigSpeed()*($this->timeType/$distance->getType());
	}
	
	/**
	 * @param Time $time
	 * @return Distance
	 */
	public function getDistance(Time $time){
		return new Distance($this->calculateForDistance($time), $this->distanceType);
	}
	
	/**
	 * @param Distance $distance
	 * @return Time
	 */
	public function getTime(Distance $distance){
		return new Time($this->calculateForTime($distance), $this->timeType);
	}
	
	protected function calculate(Number $numObj){
		if(!$this->is($numObj)) throw new UnitException("unit is incorrect!");
		$origNumber = $this->calculateOrigSpeed();
		$this->distanceType = $numObj->distanceType;
		$this->timeType = $numObj->timeType;
		return $this->calculateSpeed($origNumber);
	}
	
}