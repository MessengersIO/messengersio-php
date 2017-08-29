<?php

namespace MessengersIO\Message;

class TextMessage extends Message {

	private $text;

	function __construct($text){
		$this->text = $text;
	}

	function getText(){
		return $this->text;
	}

	function getType(){
		return 'text';
	}

	function getData(){
		return [
			'text' => $this->getText()
		];
	}

}