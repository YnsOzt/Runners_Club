<?php
class CoachController{

	public function __construct() {

	}

	public function run(){
		if(empty($_SESSION['coach'])){
				header("Location: ?action=accueil");
				die();
		}


		$plan_table = Db::getInstance()->select_all_training_plans(); //all the training plans

		//when the coach select a plan to see it's details
		$show_training_details = false;
		if(isset($_POST['seeDetails']) && !empty($_POST['detailSelected'])){
			$show_training_details = true;
			$plan_title = Db::getInstance()->select_training_id($_POST['detailSelected'])->get_plan_name();
			$plan_selected = Db::getInstance()->select_daily_training($_POST['detailSelected']); //table of daily plans
		}

		//when the coach want to delete a plan
		$notif_plan_delete = "";
		if(isset($_POST['delete']) && !empty($_POST['deletePlan'])){
			$plan_id = $_POST['deletePlan'];
			$ex_plan_followers = Db::getInstance()->select_members_participating_training($plan_id);
			for($i = 0 ; $i < count($ex_plan_followers); $i++){
				Db::getInstance()->add_trainings(1, $ex_plan_followers[$i]->get_member_id());//change the training of everone to the general one
			}
			Db::getInstance()->delete_from_daily_plan($plan_id);
			Db::getInstance()->delete_from_trainings($plan_id);
			Db::getInstance()->delete_from_training_plan($plan_id);
			$notif_plan_delete = "Suppression du plan effectué avec succès";
		}

		//When the coach delete de plan of the selected day
		$notif_daily_plan_delete = "";
		if(isset($_POST['deleteDailyPlan']) && !empty($_POST['deleteDPlan'])){
			for($i = 0; $i < count($_POST['deleteDPlan']);$i++){
				preg_match('/^(.*)\/(.*)\/(.*)$/', $_POST['deleteDPlan'][$i],$result);
				$date = "$result[3]-$result[2]-$result[1]";
				Db::getInstance()->delete_from_daily_plan_via_date($_POST['dailyPlanId'],$date);
			}
			$notif_daily_plan_delete = "Suppression d'un(des) entrainement(s) journalier effectué avec succès";
		}

		//when the coach modify a description of a plan
		$notif_daily_plan_modify = "";
		if(isset($_POST['modifyDailyPlan']) && !empty($_POST['modifyDPlan'])){
			for($i = 0; $i < count($_POST['modifyDPlan']);$i++){
				preg_match('/^(.*)\/(.*)\/(.*)$/', $_POST['dailyPlanDate'][$i],$result);
				$date = "$result[3]-$result[2]-$result[1]";
				Db::getInstance()->modify_daily_plan_description($_POST['dailyPlanId'],$date, htmlspecialchars($_POST['modifyDPlan'][$i]));
			}
			$notif_daily_plan_modify = "Modification d'entrainement(s) journalier effectué avec succès";
		}

		//when the coach add a new training
		$notif_add_daily_plan = "";
		if(isset($_POST['addPlan']) && !empty($_POST['dateOfNewPlan']) && !empty($_POST['descriptionOfNewPlan'])){
			if(preg_match('/^(.*)\/(.*)\/(.*)$/', $_POST['dateOfNewPlan'],$result)){
				$date = "$result[3]-$result[2]-$result[1]";
				$date_end_plan = Db::getInstance()->select_training_id($_POST['addPlanId'])->get_date_end();
				preg_match('/^(.*)\/(.*)\/(.*)$/', $date_end_plan,$result2);
				$date_end_plan = "$result2[3]-$result2[2]-$result2[1]";
				if(strtotime($date) < strtotime(date("Y-m-d"))){
					$notif_add_daily_plan = "Cette date est déjà passé !";
				}elseif(strtotime($date) >  strtotime($date_end_plan)){
					$notif_add_daily_plan = "Cette date dépasse la date limite du plan d'entrainement !";
				}elseif(Db::getInstance()->verify_daily_plan_date($_POST['addPlanId'], $date) == true){
					Db::getInstance()->add_daily_plan(htmlspecialchars($_POST['addPlanId']),$date, htmlspecialchars($_POST['descriptionOfNewPlan']));
					$notif_add_daily_plan = "Ajout d'un entrainement journalier effectué";
				}else{
					$notif_add_daily_plan= "Cette date est déjà utilisé !";
				}
			}else{
				$notif_add_daily_plan = "L'encodage de la date est pas correcte";
			}


		}

		//import CSV FILE
		$notif_import = "";
		if(isset($_POST['importFile'])){
			if(empty($_POST['importName'])){
				$notif_import = "Vous devez entrez un nom de fichier";
			}else{
				//create the plan
				$fichier = $_FILES['plan_file']['tmp_name'];
				$last_day_of_plan = $this->getlastday($fichier);
				$first_day_of_plan = $this->getfirstday($fichier);
				$goal = "/";
				$plan_name = htmlspecialchars($_POST['importName']);
				$date_start = "";
				$date_end = "";
				if($last_day_of_plan != null){
					preg_match('/^(.*);(.*)$/', $last_day_of_plan,$last_day);
					$goal = htmlspecialchars($last_day[2]);
					preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $last_day[1],$last_date);
					$date_end = "$last_date[3]-$last_date[2]-$last_date[1]";
				}
				if($first_day_of_plan != null){
					preg_match('/^(.*);(.*)$/', $first_day_of_plan,$first_day);
					preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $first_day[1],$first_date);
					$date_start = "$first_date[3]-$first_date[2]-$first_date[1]";
				}
				Db::getInstance()->add_training_plans($goal, $plan_name, $date_start, $date_end);
				$created_plan_id = Db::getInstance()->select_last_added_tainings_plans_id();
				$all_imported_daily_plans = $this->getalldailytraining($created_plan_id,$fichier);
				for($i = 0 ; $i < count($all_imported_daily_plans); $i++){
					preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $all_imported_daily_plans[$i]->get_date(),$date);
					$date = "$date[3]-$date[2]-$date[1]";
					Db::getInstance()->add_daily_plan($all_imported_daily_plans[$i]->get_plan_id(), $date, $all_imported_daily_plans[$i]->get_description());
				}
				$notif_import = "import réussit !";
			}
		}

		//see participating members to an training plan
		$show_training_participating_members = false;
		if(isset($_POST['seeParticipating']) && !empty($_POST['participatingSelected'])){
			$show_training_participating_members = true;
			$name_of_plan = Db::getInstance()->select_training_id($_POST['participatingSelected'])->get_plan_name();
			$table_participating_to_plan = Db::getInstance()->select_members_participating_training($_POST['participatingSelected']);
		}


		$plan_table = Db::getInstance()->select_all_training_plans(); //all the training plans


		require_once(VIEWS_PATH . 'coach.php');
	}
	public function getalldailytraining($plan_id,$csvfile){
		$tableau = array();
		if(file_exists($csvfile)){
			$fcontents = file($csvfile);
			for($i=0; $i<count($fcontents) ;$i++){
				$icontent = $fcontents[$i]; //récupère les donnés de la ligne
				preg_match('/^(.*);(.*)$/', $icontent,$result);
				if(!empty($result[2])){
					if(preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $result[1],$date)){
						$dateout = "$date[3]-$date[2]-$date[1]";
						$tableau[] = new Daily_Plan($plan_id, $result[2], $dateout);
					}
				}
			}
		}
		return $tableau;
	}

	public function getlastday($csvfile){
		if(file_exists($csvfile)){
			$fcontents = file($csvfile);
			for($i = count($fcontents)-1; $i >= 0; $i--){
				$icontent = $fcontents[$i];
				if(preg_match('/^(.*);(.*)$/', $icontent,$result)){
					if(!empty($result[2])){
						return $icontent;
					}
				}
			}
		}
		return null;
	}

	public function getfirstday($csvfile){
		if(file_exists($csvfile)){
			$fcontents = file($csvfile);
			for($i = 0; $i < count($fcontents); $i++){
				$icontent = $fcontents[$i];
				if(preg_match('/^(.*);(.*)$/', $icontent,$result)){
					if(!empty($result[2])){
						return $icontent;
					}
				}
			}
		}
		return null;
	}

}
?>
