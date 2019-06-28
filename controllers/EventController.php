<?php
class EventController{

	public function __construct() {

	}

	public function run(){
		if(empty($_SESSION['authentifie'])){
				header("Location: ?action=accueil");
				die();
		}

		$table_interrested_events_id = array(); //table that'll store the event that the member is interrested in
		$table_participating_events_id = array(); //table that'll store the event that the member is participating to

		//To add interrested events
		$notif_interest = "";
		if(isset($_POST['interrested'])){
			if(!empty($_POST['interrestedlist'])){
				foreach ($_POST['interrestedlist'] as $event) {
					$table_interrested_events_id[] = $event;
				}
			}
			Db::getInstance()->replace_interrested_event($_SESSION['info']->get_member_id(), $table_interrested_events_id);
			$notif_interest = "Votre(vos) intérêt(s) a(ont) été pris en compte";
		}

		//To add participating events
		$notif_payment = "";
		if(isset($_POST['participating'])){
			if(!empty($_POST['participatinglist'])){
				foreach ($_POST['participatinglist'] as $event) {
					$table_participating_events_id[] = $event;
				}
			}
				Db::getInstance()->replace_participating_event($_SESSION['info']->get_member_id(), $table_participating_events_id);
				$notif_payment = "Votre(vos) paiment(s) a(ont) été accepté(s) et sera(seront) effectué(s)/remboursé(s) via le compte ".$_SESSION['info']->get_account_number();

		}

		//To see the tables of participating and interresed members to an specific event
		$table_interresed_members = array();
		$table_participating_members = array();
		$last_event = "";
		$checked = false;
		if(isset($_POST['search'])){
			$checked = true;
			$last_event = htmlspecialchars($_POST['nameofevent']);
			$table_interresed_members = Db::getInstance()->select_interrested_members_event_id($last_event);
			$table_participating_members = Db::getInstance()->select_participating_members_event_id($last_event);
		}

		//To see the localisation of the event on a google map
		$show_map = false;
		$lat = 0;
		$long = 0;
		$localisation_event_name = "";
		if(isset($_POST['map']) && !empty($_POST['maplocation'])){
			$event = Db::getInstance()->select_event_id($_POST['maplocation']);
			if($event != null){
				if($event->get_latitude() != null && $event->get_longitude() != null){
					$show_map = true;
					$lat = $event->get_latitude();
					$long = $event->get_longitude();
					$localisation_event_name = $event->get_event_name();
				}
			}
		}





		//update of differents tables
		$all_events = Db::getInstance()->select_all_events();
		$events = array();//table with not out-dated events
		$passed_events = array(); //table with all the out dated events
		preg_match('/^(.*)\/(.*)\/(.*)$/', DATE,$result_today);
		$todays_date = "$result_today[3]-$result_today[2]-$result_today[1]";
		foreach ($all_events as $indice => $event) {
			preg_match('/^(.*)\/(.*)\/(.*)$/', $event->get_date_start(),$result_starting);
			$event_date = "$result_starting[3]-$result_starting[2]-$result_starting[1]";
			if(strtotime($todays_date) < strtotime($event_date)){ //if on time
				$events[] = $event;
			}else{ //if late
				$passed_events[] = $event;
			}
		}

		$interrested_events = Db::getInstance()->select_interrested_event($_SESSION['info']->get_member_id());
		$participating_events = Db::getInstance()->select_participating_event($_SESSION['info']->get_member_id());

		require_once(VIEWS_PATH . 'event.php');
	}

}
?>
