<?php

namespace MessengersIO\Message;

class LocationMessage extends Message {

	private $latitude;
	private $longitude;

	function __construct($latitude, $longitude){
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}

	function getLatitude(){
		return $this->latitude;
	}

	function getLongitude(){
		return $this->longitude;
	}

	function getType(){
		return 'location';
	}

	function getData(){
		return [
			'location' => [
				'latitude' => $this->getLatitude(),
				'longitude' => $this->getLongitude()
			]
		];
	}

}