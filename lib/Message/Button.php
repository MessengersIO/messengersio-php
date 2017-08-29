<?php

namespace MessengersIO\Message;

class Button {

	private $label;
	private $value;

	function __construct($label, $value=null){
		$this->label = $label;
		$this->value = is_null($value) ? $label : $value;
	}

	function getLabel(){
		return $this->label;
	}

	function getValue(){
		return $this->value;
	}

	function getData(){
		return [
			'label' => $this->getLabel(),
			'value' => $this->getValue()
		];
	}

}