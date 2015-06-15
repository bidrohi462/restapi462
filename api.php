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
		$this->method=$_SERVER['REQUEST_METHOD'];
		$this->endpoint=$args[0];
		$this->query=$_GET['q'];

		$this->statusHeader(200);

		switch($this->endpoint) {
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
		$answer='';
		$regex=array(
			"/What is today's temperature in (.*)\?/",
			"/What is today's humidity in (.*)\?/",
			"/Is there (.*) today in (.*)\?/"
		);
		for($i=0; $i<count($regex); $i++) {
			$r=$regex[$i];
			$city='';
			if(preg_match($r, $this->query, $array)) {
				if($i==2) { // forecast
					$city=$array[2];
				} else { // temperature or humidity
					$city=$array[1];
				}

				$weather=$this->getWeatherFor($city);
				switch($i) {
					case 0: // Temperature
					$answer=$weather->main->temp.' K';
					break;

					case 1: // Humidity
					$answer=$weather->main->humidity.'%';
					break;

					case 2: // Forecast
					$code=$weather->weather[0]->id;
					$fq=$array[1];
					if($fq=='Rain') {
						$answer=$code>=200 && $code<600? 'Yes': 'No';
					} else if($fq=='Clouds') {
						$answer=($code>=200 && $code<600) || ($code>800 && $code<900)? 'Yes': 'No';
					} else if($fq=='Clear weather') {
						$answer=$code==800? 'Yes': 'No';
					} else {
						$answer='No';
					}
					break;
				}
				break;
			}
		}
		$array=array(
			'answer' => $answer
		);
		echo json_encode($array, $this->p);
	}

	private function getAnswer() {
		$array=array(
			'answer' => 'Your majesty! Jon Snow knows nothing! So do I!'
		);
		echo json_encode($array, $this->p);
	}

	private function getWeatherFor($city) {
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.openweathermap.org/data/2.5/weather?q='.$city);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		return json_decode(curl_exec($ch));
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