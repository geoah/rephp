<?php

//5.3: namespace.

class Url extends Object { 

	private $protocol;
	private $user;
	private $pass;
	private $hostname;
	private $port;
	private $path;

	public function __construct($url = ''){
		if($url == '') throw new URLEx('Cannot create a URL object without specifying a string.'); 
		$data = parse_url($url); 
		$this->protocol = $data['scheme']; 
		if(isset($data['user'])) $this->user = $data['user']; 
		if(isset($data['pass'])) $this->pass = $data['pass']; 
		if(isset($data['host'])) $this->hostname = $data['host']; 
		if(isset($data['port'])) $this->port = $data['port']; 
		if(isset($data['path'])) $this->path = $data['path']; 
		if(isset($data['query'])) $this->query = $data['query']; 
	} 

	public function __toString(){ 
		$url = $this->protocol.'://'; 
		if(isset($this->user) && isset($this->pass)) $url .= $this->user .':'. $this->pass.'@'; 
		$url .= $this->hostname; 
		if(isset($this->port)) $url .= ':'.$this->port; 
		if(isset($this->path)) $url .= $this->path; 
		if(isset($this->query)) $url .= '?'.$this->query; 
		return $url; 
	} 

	public function GetLink($title = null, $target = '_blank', $class='link'){ 
		$url = $this->protocol.'://'; 
		if(isset($this->user) && isset($this->pass)) $url .= $this->user .':'. $this->pass.'@'; 
		$url .= $this->hostname; 
		if(isset($this->port)) $url .= ':'.$this->port; 
		if(isset($this->path)) $url .= $this->path; 
		if(isset($this->query)) $url .= '?'.$this->query; 
		if(is_null($title)) $title = $url; 
		return '<a href="'.$url.'" target="'.$target.'" class="'.$class.'">'.$title.'</a>'; 
	} 

	public function GetProtocol(){ 
		if(isset($this->protocol)) return $this->protocol; 
		else return ''; 
	} 

	public function GetUser(){ 
		if(isset($this->user)) return $this->user; 
		else return ''; 
	} 

	public function GetPass(){ 
		if(isset($this->pass)) return $this->pass; 
		else return ''; 
	} 

	public function GetHostname(){ 
		if(isset($this->hostname)) return $this->hostname; 
		else return ''; 
	} 

	public function GetPort(){ 
		if(isset($this->port)) return $this->port; 
		else return ''; 
	} 

	public function GetPath(){ 
		if(isset($this->path)) return $this->path; 
		else return ''; 
	} 
} 