<?php

namespace MessengersIO;

use MessengersIO\Message\Message;

final class Thread {

	private $id;
	private $supported;
	private $app;
	private $state;
	private $data;
	private $mustLoadNextState;

	function __construct($threadId, App $app, $supported=[]){
		$this->id = $threadId;
		$this->app = $app;
		$this->supported = $supported;
		$this->loadNextState(false);
	}

	public function send(Message $message){
		return $this->app->sendMessage($this, $message);
	}

	public function getCurrentState(){
		return $this->state;
	}

	public function getData(){
		return $this->data;
	}

	public function moveAndLoadState($stateName, $data=null){
		$this->moveToState($stateName, $data);
		$this->loadNextState();
		return $this;
	}

	public function moveToState($stateName, $data=null){
		$this->state = $stateName;
		$this->setData($data);
		return $this;
	}

	public function setData($data){
		if(! is_null($data))
			$this->data = $data;
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