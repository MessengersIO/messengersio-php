<?php

namespace MessengersIO\Message;

abstract class Message {

	private $keyboard = [[]];

	abstract function getData();
	abstract function getType();

	public function addButton(Button $button){
		$this->keyboard[count($this->keyboard)-1][] = $button->getData();
	}

	public function addButtonRow(){
		$this->keyboard[] =	[];
	}

	public function getKeyboard(){
		return [
			'buttons' => $this->keyboard
		];
	}

}