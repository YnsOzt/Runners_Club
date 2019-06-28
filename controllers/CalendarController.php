<?php
class CalendarController{

	public function __construct() {

	}

	public function run(){
		if(empty($_SESSION['authentifie'])){
				header("Location: ?action=accueil");
				die();
		}



		ob_clean(); //clean la page d'abbord
		require 'lib/iCalendar/CalendarEvent.class.php';
		require 'lib/iCalendar/Calendar.class.php';
		$plan_of_member = Db::getInstance()->select_actual_member_training(); //table of daily plans
		$array_of_event_plan = array();
		foreach ($plan_of_member as $indice => $plan) {
		preg_match('/^(.*)\/(.*)\/(.*)$/', $plan->get_date(),$result_date);
			$starting_date = "$result_date[3]-$result_date[2]-$result_date[1] 00:00:01";
			$ending_date = "$result_date[3]-$result_date[2]-$result_date[1] 23:59:59";
			$array_of_event_plan[]= CalendarEvent::createCalendarEvent(
															new DateTime($starting_date),
															new DateTime($ending_date),
															"Plan d'entrainement du jour",
															$plan->get_description(),
															"Salle de sport"
															);
		}
		$calendar = Calendar::createCalendar(
                  $array_of_event_plan,
									"Votre plan d'entrainement",
									"Site du groupe 1");

		$calendar->generateDownload();

		ob_flush(); //vide le fichier !
		@header("Location: ?action=plan");
		die();

	}
}
?>
