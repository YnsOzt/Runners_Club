<?php


	ob_start();
	
	//parsing the config.properties file
	$properties_table = array(); //the table with the differents properties in it
	if(file_exists("conf/config.properties")){
		$fcontents = file("conf/config.properties");
		for($i=0; $i<count($fcontents) ;$i++){
			$icontent = $fcontents[$i];
			preg_match('/^(.*)\=(.*)$/', $icontent,$result);
			$properties_table[$result[1]] = $result[2];
		}
	}
	define("GROUP_NAME", $properties_table['groupname']);
	define("CITY_NAME", $properties_table['cityname']);
	define("TEAM_PICTURE", $properties_table['teampicture']);
	define("EMAIL", $properties_table['email']);
	define("PHONE_NUMBER", $properties_table['phonenumber']);
	define("INVIT_MESSAGE", $properties_table['invitmessage']);


	define("DATE",date("d/m/Y"));
	define("VIEWS_PATH", "views/");
	define("CONTROLLER_PATH", "controllers/");

	function chargerClasse($classe){
		require_once('models/' .$classe.'.class.php');
	}
	spl_autoload_register('chargerClasse');

	session_start();
	require_once(VIEWS_PATH."header.php");
	$action = (isset($_GET['action'])) ? htmlentities($_GET['action']) : 'default';
	switch($action){
		case 'login' : require_once(CONTROLLER_PATH."LogInController.php");
		$controller = new LogInController();
		break;

		case 'admin' : require_once(CONTROLLER_PATH."AdminController.php");
		$controller = new AdminController();
		break;

		case 'plan' : require_once(CONTROLLER_PATH."PlanController.php");
		$controller = new PlanController();
		break;

		case 'signin' : require_once(CONTROLLER_PATH."SignInController.php");
		$controller = new SignInController();
		break;

		case 'logout' : require_once(CONTROLLER_PATH."LogOutController.php");
		$controller = new LogOutController();
		break;

		case 'account' : require_once(CONTROLLER_PATH."AccountController.php");
		$controller = new AccountController();
		break;

		case 'event' : require_once(CONTROLLER_PATH."EventController.php");
		$controller = new EventController();
		break;

		case 'coach' : require_once(CONTROLLER_PATH."CoachController.php");
		$controller = new CoachController();
		break;

		default : require_once(CONTROLLER_PATH."HomeController.php");
		$controller = new HomeController();
		break;
	}

	$controller->run();


	require_once(VIEWS_PATH."footer.php");

	ob_end_flush();
?>
