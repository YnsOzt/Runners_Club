<?php 
class LogOutController{
	
	public function __construct() {
	
	}
			
	public function run(){	
		$_SESSION = array();
		header("Location: index.php");
		die();
	}
	
}
?>