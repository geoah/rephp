<?php

//5.3: Namespace

class errorHandler {
	private $debug = 0;

	public function __construct(){
		set_error_handler(array($this, 'handleError'));
	}

	public function handleError($errorType, $errorString, $errorFile, $errorLine){	
		switch ($errorType){
			case E_USER_ERROR:
				break;
			case E_USER_WARNING:
				break;
			case E_USER_NOTICE:
				break;
		}
	}
}