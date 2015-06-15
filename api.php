<?php
class RestApi {

	public function __construct() {
		header('Content-Type: application/json');

		// request method other than GET
		if($_SERVER['REQUEST_METHOD']!=='GET') {
			$this->errorMessage('Only GET request allowed');
		}

		// call to base url of the api
		if(!isset($_GET['request']) || empty($_GET['request']) || !isset($_GET['q']) || empty($_GET['q'])) {
			$this->errorMessage('Application: http://restapi462.herokuapp.com/(endpoint)?q=(query)');
		}

		// too many parameters in path
		$args=explode('/', $_GET['request']);
		if(count($args)>1) {
			$this->errorMessage('Too many parameters in requested path');
		}

		// initialize parameters
		$method=$_SERVER['REQUEST_METHOD'];
		$endpoint=$args[0];
		$query=$_GET['q'];

		$this->statusHeader(200);

		switch($endpoint) {
			case 'greetings':
			$this->getGreetings();
			break;

			case 'weather':
			$this->getWeather();
			break;

			case 'qa':
			$this->getAnswer();
			break;

			default: // invalid endpoint
			$this->errorMessage('No such endpoint');
			break;
		}
	}

	private function getGreetings() {
		$array=array(
			'answer' => 'Hello, Kitty! Nice to meet you!'
		);
		echo json_encode($array, $this->p);
	}

	private function getWeather() {
		$array=array(
			'answer' => 'It is raining :('
		);
		echo json_encode($array, $this->p);
	}

	private function getAnswer() {
		$array=array(
			'answer' => 'Your majesty! Jon Snow knows nothing! So do I!'
		);
		echo json_encode($array, $this->p);
	}

	private function errorMessage($message, $status=400) {
		$array=array(
			'error' => $message
		);

		$this->statusHeader($status);
		echo json_encode($array, $this->p);
		die;
	}

	private function statusHeader($status) {
		$description='';

		switch($status) {
			case 200:
			$description='OK';
			break;

			case 400:
			$description="Bad Request";
			break;

			case 404:
			$description="Not Found";
			break;

			case 500:
			$description="Internal Server Error";
			break;
		}
		header("HTTP/1.1 $status $description");
	}

	private $method='';
	private $endpoint='';
	private $query='';

	private $p=JSON_PRETTY_PRINT;

}