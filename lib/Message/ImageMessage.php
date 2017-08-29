<?php

namespace MessengersIO\Message;

class ImageMessage extends Message {

	private $url;

	function __construct($url){
		$this->url = $url;
	}

	function getUrl(){
		return $this->url;
	}

	function getType(){
		return 'image';
	}

	function getData(){
		return [
			'image' => $this->getUrl()
		];
	}

}