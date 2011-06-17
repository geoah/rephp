<?php
	
	class defaultController {
		
		public function index(){
			return 'Welcome to your first application';
		}
		
		public function timesTwo($i){
			return 2 * (int) $i;
		}
		
	}
	
?>