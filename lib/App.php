<?php

namespace MessengersIO;

use MessengersIO\Message\CallbackMessage;
use MessengersIO\Message\Message;
use MessengersIO\Message\TextMessage;
use MessengersIO\Message\ImageMessage;

final class App {

	const base = "https://api.messengers.io";

	private $apiKey;
	private $defaultStateName;
	private $states = [];

	function __construct($apiKey){
		$this->apiKey = $apiKey;
	}

	public function state($stateName, $method){
		$this->states[$stateName] = $method;
	}

	public function setDefaultState($stateName){
		$this->defaultStateName = $stateName;
	}

	public function run(){

		$data = file_get_contents('php://input');
		$data = @json_decode($data, true);
		if(! $data) return false;

		$threadId = $data['user']['id'] ?? false;
		if(! $threadId) return false;

		$supported = $data['bot']['support'] ?? [];
		//$currentState = $data['user']['state'] ?? null;
		$currentState = $_GET['state'] ?? null;
		$thread = new Thread($threadId, $this, $currentState, $supported);

		$stateName = $thread->getCurrentState();
		if(! $stateName or ! isset($this->states[$stateName]))
			$stateName = $this->defaultStateName;

		$method = $this->states[$stateName] ?? null;
		if(! is_callable($method))
			return false;

		$message = $this->treatContent($thread, $data['content']);

		return $method($thread, $message);

	}

	private function treatContent(Thread $thread, $content){
		if(isset($content['text']))
			return new TextMessage($content['text']);

		if(isset($content['image']))
			return new ImageMessage($content['image']);

		if(isset($content['callback']))
			return new CallbackMessage($content['callback']);

		return false;
	}

	/* API CALLS */

	public function sendMessage(Thread $thread, Message $message){
		$data = $message->getData();
		if($thread->getCurrentState())
			$data['path'] = '/?state='.$thread->getCurrentState();

		$data['keyboard'] = $message->getKeyboard();

		return $this->POST("/send/".$thread->getId().'/'.$message->getType(), $data);
	}

	private function POST($path, $data=[], $auth=true){
		return $this->apiCall($path, $data, true, $auth);
	}

	private function apiCall($path, $data=[], $post=false, $auth=true){
		$t = self::base.$path;
		if($auth)
			$t .= "?api_key=".$this->apiKey;
		return $this->request($t,$data,$post);
	}

	private function request($url, $body, $post) {

		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL, $url);
		if($post){
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($body));
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
		));

		$response = curl_exec($ch);

		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($code < 200 or $code > 202){
			throw new \Exception($response,$code);
		}

		if ($response === false)
			throw new \Exception('Error Processing Request', 1);

		$responseData = @json_decode($response, true);
		if ($responseData === false)
			throw new \Exception('JSON decode error');

		return $responseData;
	}

}