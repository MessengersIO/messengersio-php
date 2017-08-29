<?php

namespace MessengersIO;

use MessengersIO\Message\Message;

final class Thread {

	private $id;
	private $supported;
	private $app;
	private $state;
	private $mustLoadNextState;

	function __construct($threadId, App $app, $state=null, $supported=[]){
		$this->id = $threadId;
		$this->app = $app;
		$this->supported = $supported;
		$this->state = $state;
		$this->loadNextState(false);
	}

	public function send(Message $message){
		return $this->app->sendMessage($this, $message);
	}

	public function getCurrentState(){
		return $this->state;
	}

	public function moveToState($stateName){
		$this->state = $stateName;
		return $this;
	}

	public function loadNextState($mustLoad=true){
		$this->mustLoadNextState = $mustLoad;
		return $this;
	}

	public function getLoadNextState(){
		return $this->mustLoadNextState;
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