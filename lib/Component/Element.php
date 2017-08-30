<?php

namespace MessengersIO\Component;

class Element {

	private $title;
	private $text;
	private $image;

	function __construct($title){
		$this->title = $title;
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

	public function getData(){
		return [
			'title' => $this->getTitle(),
			'text' => $this->getText(),
			'image' => $this->getImage(),
		];
	}

}