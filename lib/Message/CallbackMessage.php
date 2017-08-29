<?php

namespace MessengersIO\Message;

class CallbackMessage extends Message {

	private $value;

	function __construct($value){
		$this->value = $value;
	}

	function getValue(){
		return $this->value;
	}

	function getType(){
		return 'callback';
	}

	function getData(){
		return [
			'callback' => $this->getValue()
		];
	}

}