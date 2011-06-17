<?php

	define('REPHP_VERSION', '0.2');
	define('REPHP_DIR', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR); // 5.3: replace with __DIR__
	define('REPHP_APP_DIR', realpath('.') . DIRECTORY_SEPARATOR);
	define('REPHP_EXT', 'php');
	
	function __autoload($className){
		include(REPHP_DIR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.' . REPHP_EXT); // require costs too much for something like this.
	}