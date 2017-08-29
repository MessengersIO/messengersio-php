<?php

namespace MessengersIO\Message;

abstract class Message {

	private $keyboard = [[]];

	private $treated = false;

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

	public function setTreated($treated=true){
		$this->treated = $treated;
	}

	public function isTreated(){
		return $this->treated;
	}

}