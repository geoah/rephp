<?php

//5.3: namespace.

class router{
	private $controller = null;
	
	private $route = null; // actuall string from mod_rewrite
	private $routes = array(); // routes for custom urls
	
	private $controllerName = null;
	private $methodName = null;
	private $arguments = array();
	
	public $camelCaseDelimeter = '-';
	
	function __construct($route=null){
		if($route!==null){
			$this->route = $route;
		}elseif(isset($_GET['route'])){
			$this->route = $_GET['route'];
		}
		$this->routeParts = $this->resolveRoute();
	}
	
	public function __toString(){
		return (string) $this->initController();
	}
	
	public function addRoute($route, $options){
		//todo:
	}
	
	public function removeRoute($route){
		//todo: unset($this->routes[$route]);
	}
	
	private function resolveRoute(){
		if(strstr($this->route, '/')){
			$parts = explode('/', $this->route);
		}else{
			$parts = array();
			$parts[0] = $this->route;
		}
		
		$controllerNameDefault = 'default';
		$methodNameDefault = 'index';
		
		// Assume that the controller has a default name
		$this->controllerName = $this->checkController($controllerNameDefault);
		// If url may have a controller, check if it exists
		if(isset($parts[0])){
			// Set as the active method and remove from parts
			$this->controllerName = $this->checkController($parts[0]) ? array_shift($parts) : $this->controllerName;
		}
		// If all fails.
		if($this->controllerName===false){
			die('Invalid controller [' . $this->controllerName . ']');
		}
		
		// Construct the controller object
		$this->constructController();
		
		// Assume that the method has a default name
		$this->methodName = $this->checkMethod($methodNameDefault);
		// If url may have a method, check if it exists
		if(isset($parts[0])){
			// Set as the active controller and remove from parts
			// Method name might be a camel case variant so we must use the check result that always
			//   returns the actual object's method name.
			$finalMethodName = $this->checkMethod($parts[0]);
			if($finalMethodName!==false){
				$this->methodName = $finalMethodName;
				array_shift($parts);
			}
		}
		
		// If all fails.
		if($this->methodName===false){
			die('Invalid method on controller [' . $this->controllerName . ']');
		}
		
		//debug: echo $this->controllerName . '::' . $this->methodName;
				
		$this->arguments = $parts;
	}
	
	private function checkController($controllerName){
		$controllerPath = REPHP_APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . $controllerName . 'Controller.php';
		if(file_exists($controllerPath)){
			require_once($controllerPath);
			//todo: Catch on error.
			return $controllerName;
		}else{
			return false;
		}
	}
	
	//todo: This must be replaced by the Url class.
	public function getBaseUrl(){
		$pageURL = 'http';
		if (@$_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		$pageURL = str_replace('/'.$this->route, '/', $pageURL);
		return $pageURL;
	}
	
	private function checkMethod($methodName){
		// is_callable fucks up
		if(method_exists($this->controller, $methodName)){
			return $methodName;
		}else{
			if(stristr($methodName, $this->camelCaseDelimeter)){
				$camelCaseMethodName = $methodName;
				$camelCaseMethodName = str_replace($this->camelCaseDelimeter, ' ', $camelCaseMethodName);
				$camelCaseMethodName = ucwords($camelCaseMethodName);
				$camelCaseMethodName = str_replace(' ', '', $camelCaseMethodName);
				//5.3: $camelCaseMethodName = lcfirst($camelCaseMethodName);
				// This is a replacement till moved to php5.3
				$camelCaseMethodName{0} = strtolower($camelCaseMethodName{0});
				if(method_exists($this->controller, $camelCaseMethodName)){
					return $camelCaseMethodName;
				}
			}
			return false;
		}
	}
	
	private function constructController(){
		$controllerNameFull = $this->controllerName . 'Controller';
		$this->controller = new $controllerNameFull;
	}

	private function initController(){
		
		// Execute before method
		if($this->checkMethod('_before')){
			call_user_func_array(
				array(
					$this->controller,
					'_before'
				), array()
			);
		}
		
		// Execute main method
		call_user_func_array(
			array(
				$this->controller,
				$this->methodName
			),
			$this->arguments
		);
		
		// Execute fter method
		if($this->checkMethod('_after')){
			call_user_func_array(
				array(
					$this->controller,
					'_after'
				), array()
			);
		}
		
	}
	
}	