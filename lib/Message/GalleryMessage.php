<?php

namespace MessengersIO\Message;

class GalleryMessage extends CollectionMessage {

	function __construct(){
		$this->isGallery = true;
		$this->type = 'gallery';
	}

}