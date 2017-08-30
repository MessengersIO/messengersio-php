<?php

namespace MessengersIO\Message;

use MessengersIO\Component\Element;

abstract class CollectionMessage extends Message {

	private $elements = [];
	protected $isGallery = false;
	protected $type;

	function addElement(Element $element){
		$this->elements[] = $element;
	}

	function getType(){
		return 'list';
	}

	function getData(){
		$elements = [];
		foreach($this->elements as $el)
			$elements[] = $el->getData();

		return [
			"list" => [
				'gallery' => $this->isGallery,
				'type' => $this->type,
				'elements' => $elements
			]
		];
	}

}