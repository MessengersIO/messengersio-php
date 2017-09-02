<?php

namespace MessengersIO\Component;

use MessengersIO\Message\Button;

class Element {

	private $title;
	private $text;
	private $image;
	private $buttons;

	function __construct($title){
		$this->title = $title;
		$this->buttons = [];
	}

	function setImage($image){
		$this->image = $image;
		return $this;
	}

	function getImage(){
		return $this->image;
	}

	function setText($text){
		$this->text = $text;
		return $this;
	}

	function getText(){
		return $this->text;
	}

	function getTitle(){
		return $this->title;
	}

	function addButton(Button $button){
		$this->buttons[] = $button;
	}

	public function getData(){

		$buttons = [];
		foreach($this->buttons as $button)
			$buttons[] = $button->getData();

		return [
			'title' => $this->getTitle(),
			'text' => $this->getText(),
			'image' => $this->getImage(),
			'buttons' => $buttons
		];
	}

}