<?php
class PlanController{

	public function __construct() {

	}

	public function run(){
		if(empty($_SESSION['authentifie'])){
				header("Location: ?action=accueil");
				die();
		}

		$plan_table = Db::getInstance()->select_all_training_plans(); //all the training plans

		#EXTRACT FROM ALL THE PLANS ONLY THE NONE OUTDATED PLANS
		$following_plan_table = array();//table with not out-dated plans
		preg_match('/^(.*)\/(.*)\/(.*)$/', DATE,$result_today);
		$todays_date = "$result_today[3]-$result_today[2]-$result_today[1]";
		foreach ($plan_table as $indice => $plan) {
			preg_match('/^(.*)\/(.*)\/(.*)$/', $plan->get_date_end(),$result_starting);
			$plans_date = "$result_starting[3]-$result_starting[2]-$result_starting[1]";
			if(strtotime($todays_date) < strtotime($plans_date)){
				$following_plan_table[] = $plan;
			}
		}

		//when the member select a plan to see it's details
		$show_training_details = false;
		if(isset($_POST['seeDetails']) && !empty($_POST['detailSelected'])){
			$show_training_details = true;
			$plan_title = Db::getInstance()->select_training_id($_POST['detailSelected'])->get_plan_name();
			$plan_selected = Db::getInstance()->select_daily_training($_POST['detailSelected']); //table of daily plans
		}

		//when the member change his training plan
		$notif_training_change = "";
		if(isset($_POST['choosedPlan']) && !empty($_POST['planSelected'])){
			if($_POST['planSelected'] != Db::getInstance()->select_members_following_training($_SESSION['info']->get_member_id())){
				Db::getInstance()->add_trainings($_POST['planSelected'], $_SESSION['info']->get_member_id());
				$notif_training_change = "Changement effectué";
			}else{
				$notif_training_change = "Vous suivez déjà ce plan d'entrainement !";
			}
		}




		//TO SHOW THE WEEKLY / DAILY/COMPLETE PLAN OF THE CONNECTED MEMBER
		$show_daily_training = true;
		$show_weekly_training = false;
		$show_complete_training = false;
		$actual_member_plan_id = Db::getInstance()->select_members_following_training($_SESSION['info']->get_member_id());
		$actual_member_following_plan = Db::GetInstance()->select_training_id($actual_member_plan_id);
		$plan_of_member = Db::getInstance()->select_actual_member_training(); //table of daily plans
		$plan_of_member_week = array(); //tabl of daily plan for the week
		if(isset($_POST['daily'])){
			$show_daily_training = true;
			$show_weekly_training = false;
			$show_complete_training = false;
		}elseif(isset($_POST['weekly'])){
			$show_daily_training = false;
			$show_weekly_training = true;
			$show_complete_training = false;
		}elseif(isset($_POST['complete'])){
			$show_daily_training = false;
			$show_weekly_training = false;
			$show_complete_training = true;
		}
		if($show_daily_training){
			foreach ($plan_of_member as $indice => $plan){
				if($plan->get_date() == DATE){
					$plan_of_the_day = $plan->get_description();
				}
			}
		}elseif($show_weekly_training){
			if(date('N') > 1){
				$monday_of_the_week = date('Y/m/d', strtotime("last Monday"));
			}else{
				$monday_of_the_week = date('Y/m/d', strtotime("this Monday"));
			}
			$sunday_of_the_week = date('Y/m/d', strtotime("this Sunday"));
			foreach ($plan_of_member as $indice => $plan){
				preg_match('/^(.*)\/(.*)\/(.*)$/', $plan->get_date(),$result_date);
				$plan_date = "$result_date[3]-$result_date[2]-$result_date[1]";
				if(strtotime($plan_date)>= strtotime($monday_of_the_week) && strtotime($plan_date) <= strtotime($sunday_of_the_week)){
					$plan_of_member_week[] = $plan;
				}
			}
		}

		//if a member want to export the plan
		if(isset($_POST['export'])){
			require_once(CONTROLLER_PATH."CalendarController.php");
			$controller = new CalendarController();
			$controller->run();
			die();
		}


		require_once(VIEWS_PATH . 'plan.php');

	}
}
?>
