<?php

namespace MessengersIO\Message;

class LocationMessage extends Message {

	private $latitude;
	private $longitude;

	function __construct($location){
		$this->latitude = $location['latitude'];
		$this->longitude = $location['longitude'];
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
