<?php

namespace MessengersIO\Message;

class ListMessage extends CollectionMessage {

	function __construct(){
		$this->isGallery = false;
		$this->type = 'list';
	}

}