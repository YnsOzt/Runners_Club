<?php
class Db{
	private $_db;
	private static $instance = null;

	private function __construct(){
		try{
			$this->_db = new PDO('mysql:host=localhost;dbname=projet_web;charset=utf8','root','');
			$this->_db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			$this->_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);//tableau d'objet, pas un tableau de tab
		}catch(PDOException $e){
			die('Erreur de connexion à la base de données : '.$e->getMessage());
		}
	}

	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new Db();
		}
		return self::$instance;
	}

	//function that return an array with all the members
	public function select_all_members(){
		$query = 'SELECT * FROM members ORDER BY member_id asc';
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row= $result->fetch()){
				$table[] = new Member($row->member_id, $row->last_name, $row->first_name,$row->mail, $row->picture,
				 														 $row->password, $row->adress, $row->phone_number, $row->account_number,
																		 $row->responsablity, $row->accepted, $row->contributed);
			}
		}
		return $table;
	}

	//function that return the member which correspond to the given parameters or null if it doesn't exist
	public function select_member($member_id){
		$query = "SELECT * FROM members WHERE member_id = $member_id";
		$result = $this->_db->prepare($query);
		$result->execute();
		if($result->rowcount() != 0){
			while($row = $result->fetch()){
				return new Member($row->member_id, $row->last_name, $row->first_name,$row->mail, $row->picture,
				 														 $row->password, $row->adress, $row->phone_number, $row->account_number,
																		 $row->responsablity, $row->accepted, $row->contributed);
			}
		}
		return null;
	}

	//function that return true if the parameter given correspond to an account of a member
	public function validate_member($mail, $password){
		$members = Db::getInstance()->select_all_members();
		for($i = 0 ; $i < count($members);$i++){
			if($members[$i]->get_mail() == $mail && password_verify($password, $members[$i]->get_password())){
				return $members[$i];
			}
		}
		return null;
	}

	//function that update the member that passed in the parametre
	public function update_member_info($member_id, $last_name, $first_name, $mail, $adress, $phone_number, $account_number){
		$query = "UPDATE members
							SET last_name = '$last_name', first_name = '$first_name',
							mail = '$mail', adress = '$adress', phone_number = '$phone_number',
							account_number = '$account_number'
							WHERE member_id = '$member_id' ";
		$this->_db->prepare($query)->execute();
	}

	//function that update the profile picture of the member which correpond to the ID given in the parameters
	public function update_profile_picture($member_id, $picture){
		$query = "UPDATE members SET picture = '$picture' WHERE member_id = '$member_id'";
		$this->_db->prepare($query)->execute();
	}


	//function that update the password of the member which correspond to the ID given in the parameters
	public function update_password($member_id, $password){
		$query = "UPDATE members SET password = '$password' WHERE member_id = '$member_id'";
		$this->_db->prepare($query)->execute();
	}

	//function that return every events that exists in the data base
	public function select_all_events(){
		$query = 'SELECT * FROM events ORDER BY date_start asc';
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row= $result->fetch()){
				$date_start = $row->date_start;
				$date_end = $row->date_end;
				if(preg_match('/^(.*)-(.*)-(.*)$/', $date_start, $result_date_start)){
					$date_start = "$result_date_start[3]/$result_date_start[2]/$result_date_start[1]";
				}
				if(preg_match('/^(.*)-(.*)-(.*)$/', $date_end, $result_date_end)){
					$date_end = "$result_date_end[3]/$result_date_end[2]/$result_date_end[1]";
				}

				$table[] = new Event($row->event_id, $date_start, $date_end, $row->event_name, $row->description,
														 $row->location, $row->url, $row->cost, $row->lattitude, $row->longitude, $row->url_picture);
			}
		}
		return $table;
	}

	//function that return the event corresponding to the given id
	public function select_event_id($event_id){
		$all_events = $this->select_all_events();
		for($i=0; $i<count($all_events);$i++){
			if($all_events[$i]->get_event_id() == $event_id){
				return $all_events[$i];
			}
		}
		return null;
	}

	//function that returns the event_id of the events that the member get by his member_id is interresed
	public function select_interrested_event($member_id){
		$query = "SELECT DISTINCT E.event_id
							FROM members M, interrested_members IM, events E
							WHERE M.member_id = IM.member_id AND E.event_id = IM.event_id AND M.member_id = $member_id";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row = $result->fetch()){
				$table[] = $row->event_id;
			}
		}

		return $table;
	}

	//function that returns the event_id of the events that the member get by his member_id is participating
	public function select_participating_event($member_id){
		$query = "SELECT DISTINCT E.event_id
							FROM members M, participating_members PM, events E
							WHERE M.member_id = PM.member_id AND E.event_id = PM.event_id AND M.member_id = $member_id";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row = $result->fetch()){
				$table[] = $row->event_id;
			}
		}

		return $table;
	}

	//function that returns all the event that the actual member is participating into
	public function select_acutal_members_event(){
		$events_id = $this->select_participating_event($_SESSION['info']->get_member_id());
		$table = array();
		for($i = 0 ; $i < count($events_id); $i++){
			$table[] = $this->select_event_id($events_id[$i]);
		}
		return $table;
	}

	//function that delete all the events interrested event of $member_id
	private function delete_all_interrested_event($member_id){
		$query = "DELETE FROM interrested_members WHERE interrested_members.member_id = $member_id";
		$this->_db->prepare($query)->execute();
	}

	//function that add the $event to the interrested event of $member_id
	private function add_interrested_event($member_id, $event_id){
		$query = "INSERT INTO interrested_members VALUES ($member_id,$event_id ) ";
		$this->_db->prepare($query)->execute();
	}

	//function that replace the interrested events of the member by the $table_event
	public function replace_interrested_event($member_id ,$table_event_id){
		$this->delete_all_interrested_event($member_id);
		for($i = 0 ; $i < count($table_event_id); $i++){
			$this->add_interrested_event($member_id, $table_event_id[$i]);
		}
	}

	//function that delete all the events participating event of $member_id
	private function delete_all_participating_event($member_id){
		$query = "DELETE FROM participating_members WHERE participating_members.member_id = $member_id";
		$this->_db->prepare($query)->execute();
	}

	//function that add the $event to the participating event of $member_id with has_paid = false
	private function add_participating_event_hasnt_paid($member_id, $event_id){
		$query = "INSERT INTO participating_members VALUES ($member_id,$event_id, 0) ";
		$this->_db->prepare($query)->execute();
	}

	//function that add the $event to the participating event of $member_id with has_paid = true
	private function add_participating_event_has_paid($member_id, $event_id){
		$query = "INSERT INTO participating_members VALUES ($member_id,$event_id, 1) ";
		$this->_db->prepare($query)->execute();
	}

	//function that get the id of member who has paid their event
	private function select_event_has_paid($member_id){
		$query = "SELECT event_id
							FROM participating_members
							WHERE has_paid = 1 AND member_id = $member_id";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row = $result->fetch()){
				$table[] = $row->event_id;
			}
		}

		return $table;
	}

	//function that replace the participating events of the member by the $table_event
	public function replace_participating_event($member_id ,$table_event_id){
		$event_has_paid = $this->select_event_has_paid($member_id);
		$this->delete_all_participating_event($member_id);
		for($i = 0 ; $i < count($table_event_id); $i++){
			if(in_array($table_event_id[$i], $event_has_paid)){
				$this->add_participating_event_has_paid($member_id, $table_event_id[$i]);
			}else{
				$this->add_participating_event_hasnt_paid($member_id, $table_event_id[$i]);
			}
		}
	}

	//function that returns the event_id which correspond to the given parameters
	private function searched_event_id($event_name){
		$all_events = $this->select_all_events();
		for($i = 0; $i < count($all_events) ; $i++){
			if($all_events[$i]->get_event_name() == $event_name ){
				return $all_events[$i]->get_event_id();
			}
		}
		return null;
	}
	//function that return a table of members which participate to an event
	public function select_participating_members_event_id($event_name){
		$event_id = $this->searched_event_id($event_name);
		$query = "SELECT M.*
							FROM members M, participating_members PM
							WHERE M.member_id = PM.member_id AND PM.event_id = $event_id AND M.member_id = PM.member_id";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount() != 0){
			while($row = $result->fetch()){
				$table[] = new Member($row->member_id, $row->last_name, $row->first_name,$row->mail, $row->picture,
				 											$row->password, $row->adress, $row->phone_number, $row->account_number,
														  $row->responsablity, $row->accepted, $row->contributed);
			}
		}
		return $table;
	}

	//function that return a table of members which participate to an event
	public function select_interrested_members_event_id($event_name){
		$event_id = $this->searched_event_id($event_name);
		$query = "SELECT M.*
							FROM members M, interrested_members IM
							WHERE M.member_id = IM.member_id AND IM.event_id = $event_id AND M.member_id = IM.member_id";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount() != 0){
			while($row = $result->fetch()){
				$table[] = new Member($row->member_id, $row->last_name, $row->first_name,$row->mail, $row->picture,
				 											$row->password, $row->adress, $row->phone_number, $row->account_number,
														  $row->responsablity, $row->accepted, $row->contributed);
			}
		}
		return $table;
	}

	//function that insert a new member into the DB.
	public function insert_members($lastname, $firstname, $adress, $mail, $password, $phone_number, $account, $profil_pic){
		$query = "INSERT INTO members (last_name, first_name, mail, picture, password, adress, phone_number, account_number, responsablity, accepted, contributed)
			VALUES (".$this->_db->quote($lastname).','. $this->_db->quote($firstname).','. $this->_db->quote($mail).','. $this->_db->quote($profil_pic).','.
			 				$this->_db->quote($password).','. $this->_db->quote($adress).','. $this->_db->quote($phone_number).','. $this->_db->quote($account).','.
							 $this->_db->quote('member').','. '0'.','. '0'.") ";

		$this->_db->prepare($query)->execute();
	}


	//function that select all the new member that they are not accepted.
	public function select_new_members (){
		$query = 'SELECT * FROM members WHERE accepted = 0';
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row= $result->fetch()){
				$table[] = new Member($row->member_id, $row->last_name, $row->first_name, $row->mail, $row->picture, $row->password, $row->adress, $row->phone_number,
					$row->account_number, $row->responsablity, $row->accepted, $row->contributed);
			}
		}
		return $table;
	}

	//function that update the accepted status of the member to 1(true).
	public function accept_new_member($member_id){
		$query = "UPDATE members SET accepted = 1 WHERE member_id = '$member_id'";
		$this->_db->prepare($query)->execute();
	}



	/*PARTIE SAKIR */
	//function that select all the training plans
	public function select_all_training_plans(){
		$query = "SELECT * FROM training_plans ORDER BY date_start ASC";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row= $result->fetch()){
				$table[] = new Training_Plan($row->plan_id,$row->goal, $row->plan_name, $row->date_start, $row->date_end);
			}
		}
		return $table;
	}


	//function that return the training which correspond to the $plan_id
	public function select_training_id($plan_id){
		$all_plans = $this->select_all_training_plans();
		for($i = 0 ; $i < count($all_plans); $i++){
			if($all_plans[$i]->get_plan_id() == $plan_id){
				return $all_plans[$i];
			}
		}
		return null;
	}

	//function that return a table with the daily training of a plan
	public function select_daily_training($plan_id){
		$query = "SELECT * FROM daily_plans WHERE plan_id = $plan_id";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row= $result->fetch()){
				$table[] = new Daily_Plan($row->plan_id,$row->training_description,$row->date);
			}
		}
		return $table;
	}

	//function that returns the id of the training that the actual member is following
	public function get_trainings_id($member_id){
		$query = "SELECT training_id FROM trainings WHERE member_id = $member_id AND training_end_date IS NULL";
		$result = $this->_db->prepare($query);
		$result->execute();
		if($result->rowcount()!=0){
			while($row= $result->fetch()){
				return $row->training_id;
			}
		}
		return null;
	}

	//function that returns the id of the training_plan that the actual member is following
	public function select_members_following_training($member_id){
		$query = "SELECT plan_id FROM trainings WHERE member_id = $member_id AND training_end_date IS NULL";
		$result = $this->_db->prepare($query);
		$result->execute();
		if($result->rowcount()!=0){
			while($row= $result->fetch()){
				return $row->plan_id;
			}
		}
		return null;
	}

	//function that update the ending date of the training that the $member_id is following
	public function update_training_end_date($plan_id,$member_id){
		$training_id = $this->get_trainings_id($member_id);
		$todays_date = date("Y-m-d");
		if($training_id != null){
			$query = "UPDATE trainings SET training_end_date = '$todays_date' WHERE training_id = $training_id";
			$this->_db->prepare($query)->execute();
		}
	}

	//function that add a training that the member is following
	public function add_trainings($plan_id,$member_id){
		$this->update_training_end_date($plan_id, $member_id);
		$todays_date = date("Y-m-d");
		$query = "INSERT INTO trainings (member_id, plan_id, training_start_date)
							VALUES(".$this->_db->quote($member_id).','.$this->_db->quote($plan_id).','.$this->_db->quote($todays_date).")";
		$this->_db->prepare($query)->execute();
	}

	//return a table with all the training that the actual member has to do
	public function select_actual_member_training(){
		$plan_id = $this->select_members_following_training($_SESSION['info']->get_member_id());
		$query = "SELECT * FROM daily_plans WHERE plan_id = $plan_id";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row = $result->fetch()){
				$table[] = new Daily_Plan($row->plan_id,$row->training_description,$row->date);
			}
		}
		return $table;
	}

	//delete a whole training from training plans
	public function delete_from_training_plan($plan_id){
		$query = "DELETE FROM training_plans WHERE plan_id = $plan_id";
		$this->_db->prepare($query)->execute();
	}
	//delete all the daily plans from the table daily_plans
	public function delete_from_daily_plan($plan_id){
		$query = "DELETE FROM daily_plans WHERE plan_id = $plan_id";
		$this->_db->prepare($query)->execute();
	}

	public function delete_from_trainings($plan_id){
		$query = "DELETE FROM trainings WHERE plan_id = $plan_id";
		$this->_db->prepare($query)->execute();
	}

	//delete one daily plan via date
	public function delete_from_daily_plan_via_date($plan_id,$date){
		$query = "DELETE FROM daily_plans WHERE plan_id = $plan_id AND date = '$date'";
		$this->_db->prepare($query)->execute();
	}

	//modify one description of a daily plan
	public function modify_daily_plan_description($plan_id,$date,$description){
		$query = "UPDATE daily_plans SET training_description = ".$this->_db->quote($description)." WHERE plan_id = $plan_id AND date = '$date' ";
		$this->_db->prepare($query)->execute();
	}

	//add a new daily_training
	public function add_daily_plan($plan_id, $date, $description){
		$query = "INSERT INTO daily_plans VALUES(".$this->_db->quote($date).",".$plan_id.",".$this->_db->quote($description).")";
		$this->_db->prepare($query)->execute();
	}

	//add a new training plan
	public function add_training_plans($goal, $plan_name, $date_start, $date_end){
		$query = "INSERT INTO training_plans(goal, plan_name, date_start, date_end) VALUES(".$this->_db->quote($goal).",".
		$this->_db->quote($plan_name).",".$this->_db->quote($date_start).",".$this->_db->quote($date_end).")";
		$this->_db->prepare($query)->execute();
	}

	//select last added trainig plan id
	public function select_last_added_tainings_plans_id(){
		$query = 'SELECT max(plan_id) as "plan_id" FROM training_plans';
		$result = $this->_db->prepare($query);
		$result->execute();
		if($result->rowcount()!=0){
			$row = $result->fetch();
			return $row->plan_id;
		}
		return null;
	}

	//select all the members that are participating to the training plan which correspond to the paramets
	public function select_members_participating_training($plan_id){
		$query = "SELECT member_id FROM trainings WHERE plan_id = $plan_id AND training_end_date IS NULL";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row = $result->fetch()){
				$table[] = $this->select_member($row->member_id);
			}
		}
		return $table;
	}

	//check if the date entered is not already in the daily plan_name
	public function verify_daily_plan_date($plan_id, $date){
		$query = "SELECT * FROM daily_plans WHERE plan_id = $plan_id AND date = '$date'";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			return false;
		}
		return true;
	}

	//select id of event that the member HAS_PAID
	public function select_paid_participating_events($member_id){
		$query = "SELECT event_id FROM participating_members WHERE member_id = $member_id AND has_paid = 1";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row = $result->fetch()){
				$table[] = $row->event_id;
			}
		}
		return $table;
	}



	/*PARTIE SAKIR */

	/* Partie Andy*/

	public function select_no_contributors($year){
		$query = "SELECT * FROM members me, annual_contributions ac, contributions co WHERE me.member_id = ac.member_id AND co.year = ac.year AND ac.year = $year AND ac.amount_paid < co.price";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row= $result->fetch()){
				$table[] = new Member($row->member_id, $row->last_name, $row->first_name, $row->mail, $row->picture, $row->password, $row->adress, $row->phone_number,
					$row->account_number, $row->responsablity, $row->accepted, $row->contributed);
			}
		}
		return $table;
	}

	public function update_contributed_member($member_id){
		$query = "UPDATE members SET contributed = 1 WHERE member_id = '$member_id'";
		$this->_db->prepare($query)->execute();
	}

	public function insert_a_contributions($year, $member_id){
		$query = "INSERT INTO annual_contributions VALUES($year, $member_id,0)";
		$this->_db->prepare($query)->execute();
	}

	public function insert_new_contribution($year, $price){
		$query = "INSERT INTO contributions VALUES($year, $price)";
		$this->_db->prepare($query)->execute();
	}

	public function update_all_contributors(){
		$query = "UPDATE members SET contributed = 0 ";
		$this->_db->prepare($query)->execute();
	}

	public function select_year_contribution(){
		$query = "SELECT year FROM contributions ORDER BY year DESC";
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row= $result->fetch()){
				$table[] = $row->year;
			}
		}
		return $table;
	}

	public function select_max_year_contribution(){
		$query = 'SELECT max(year) as "year" FROM contributions';
		$result = $this->_db->prepare($query);
		$result->execute();
		$row = $result->fetch();
		return $row->year;
	}

	public function update_contributors($year, $member_id, $amount_paid){
		$query = "UPDATE annual_contributions SET amount_paid = $amount_paid WHERE year = $year AND member_id = $member_id";
		$this->_db->prepare($query)->execute();
	}

	public function select_price_contribution($year){
		$query = "SELECT price FROM contributions WHERE year = $year";
		$result = $this->_db->prepare($query);
		$result->execute();

		return $result->fetch()->price;
	}

	public function select_all_accepted_member(){
		$query = 'SELECT * FROM members WHERE accepted = 1';
		$result = $this->_db->prepare($query);
		$result->execute();
		$table = array();
		if($result->rowcount()!=0){
			while($row= $result->fetch()){
				$table[] = new Member($row->member_id, $row->last_name, $row->first_name,$row->mail, $row->picture,
				 														 $row->password, $row->adress, $row->phone_number, $row->account_number,
																		 $row->responsablity, $row->accepted, $row->contributed);
			}
		}
		return $table;
	}

	public function select_amount_paid($member_id, $year){
		$query = "SELECT amount_paid FROM annual_contributions WHERE member_id = $member_id AND year = $year";
		$result = $this->_db->prepare($query);
		$result->execute();

		$row = $result->fetch();
		return $row ->amount_paid;
	}

	public function count_responsable_members(){
		$query = 'SELECT count(*) as "number_of_responsable_member" FROM members WHERE 	lower(responsablity) <> "member"';
		$result = $this->_db->prepare($query);
		$result->execute();

		$row = $result->fetch();
		return $row ->number_of_responsable_member;
	}

	public function update_responsability($member_id, $responsability){
		$query = "UPDATE members SET responsablity = ".$this->_db->quote($responsability). "WHERE member_id = $member_id";
		$this->_db->prepare($query)->execute();
	}

	public function insert_to_default_plan($member_id){
		$date = date("Y-m-d");
		$query = "INSERT INTO trainings (member_id, plan_id, training_start_date) VALUES($member_id, 1, $date)";
		$this->_db->prepare($query)->execute();
	}

	public function select_unpaid_member($event_id){
		$query = "SELECT members.* FROM members, participating_members WHERE members.member_id = participating_members.member_id AND participating_members.event_id = $event_id AND has_paid = 0";
		$result = $this->_db->prepare($query);
		$result->execute();

		$table = array();
		if($result->rowcount()!=0){
			while($row= $result->fetch()){
				$table[] = new Member($row->member_id, $row->last_name, $row->first_name,$row->mail, $row->picture,
				 														 $row->password, $row->adress, $row->phone_number, $row->account_number,
																		 $row->responsablity, $row->accepted, $row->contributed);
			}
		}
		return $table;
	}

	public function select_event_by_event_name($event_name){
		$query = "SELECT * FROM events WHERE event_name = " . $this->_db->quote($event_name);
		$result = $this->_db->prepare($query);
		$result->execute();
		$row = $result->fetch();
		$date_start = $row->date_start;
		$date_end = $row->date_end;
		if(preg_match('/^(.*)-(.*)-(.*)$/', $date_start, $result_date_start)){
			$date_start = "$result_date_start[3]/$result_date_start[2]/$result_date_start[1]";
		}
		if(preg_match('/^(.*)-(.*)-(.*)$/', $date_end, $result_date_end)){
			$date_end = "$result_date_end[3]/$result_date_end[2]/$result_date_end[1]";
		}
		return new Event($row->event_id, $date_start, $date_end, $row->event_name, $row->description,
			$row->location, $row->url, $row->cost, $row->lattitude, $row->longitude, $row->url_picture);
	}

	public function update_paied_participating_event($member_id, $event_id){
		$query = "UPDATE participating_members SET has_paid = 1 WHERE member_id = '$member_id' AND event_id = $event_id";
		$this->_db->prepare($query)->execute();
	}

	public function insert_new_invent($name_new_event, $location_new_event, $description, $url_new_event, $longitude_new_event, $latitude_new_event, $cost, $start_date_new_event, $end_date_new_event){
		$query = "INSERT INTO events(date_start, date_end, event_name, description, location, url, cost, lattitude, longitude)
		VALUES (" . $this->_db->quote($start_date_new_event) . "," .$this->_db->quote($end_date_new_event) . "," .$this->_db->quote($name_new_event) . "," .$this->_db->quote($description)
		. "," . $this->_db->quote($location_new_event) . ", :url, $cost , :latitude_new_event, :longitude_new_event)";
		$qp = $this->_db->prepare($query);
		$qp->bindValue(":url", $url_new_event);
		$qp->bindValue(":latitude_new_event", $latitude_new_event);
		$qp->bindValue(":longitude_new_event", $longitude_new_event);
		$qp->execute();
	}

	public function update_event($event_id, $name_update_event, $location_update_event, $description, $url_update_event, $longitude_update_event, $latitude_update_event, $cost, $start_date_update_event, $end_date_update_event){
		$query = "UPDATE events SET date_start = " . $this->_db->quote($start_date_update_event) . ", date_end = ".$this->_db->quote($end_date_update_event) . ", event_name = " .$this->_db->quote($name_update_event) . ", description = " .$this->_db->quote($description)
		. ", location = " . $this->_db->quote($location_update_event) . ", url = :url, cost = $cost , lattitude = :latitude_update_event, longitude = :longitude_update_event WHERE event_id = '$event_id'";
		$qp = $this->_db->prepare($query);
		$qp->bindValue(":url", $url_update_event);
		$qp->bindValue(":latitude_update_event", $latitude_update_event);
		$qp->bindValue(":longitude_update_event", $longitude_update_event);
		$qp->execute();
	}

	public function set_url_picture($url_picture, $event_id){
		$query = "UPDATE events SET url_picture = " . $this->_db->quote($url_picture) . " WHERE event_id = $event_id";
		$this->_db->prepare($query)->execute();
	}
	/* end */


}
?>
