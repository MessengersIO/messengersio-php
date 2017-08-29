<?php

namespace MessengersIO;

use MessengersIO\Message\Message;

final class Thread {

	private $id;
	private $supported;
	private $app;
	private $state;

	function __construct($threadId, App $app, $state=null, $supported=[]){
		$this->id = $threadId;
		$this->app = $app;
		$this->supported = $supported;
		$this->state = $state;
	}

	public function reply(Message $message){
		return $this->app->sendMessage($this, $message);
	}

	public function getCurrentState(){
		return $this->state;
	}

	public function moveToState($stateName){
		$this->state = $stateName;
		return $this;
	}

	public function getId(){
		return $this->id;
	}

	public function supports($name){
		return isset($supported[$name]);
	}

	public function supportsText(){ return $this->supports('text'); }
	public function supportsImage(){ return $this->supports('image'); }
	public function supportsLocation(){ return $this->supports('location'); }

}