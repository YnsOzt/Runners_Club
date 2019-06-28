<?php
class AdminController{

	private $_db;
	private $_raw_html;
	public function __construct() {
		$this->_db = Db::getInstance();
		$this->_raw_html = "";
	}

	public function run(){
		if(empty($_SESSION['specialMembre']) || $_SESSION['specialMembre'] == false){
				header("Location: ?action=accueil");
				die();
		}

		$show_validate_member = true;
		$show_change_role = false;
		$show_not_in_order = false;
		$show_create_contribution = false;
		$show_create_event = false;
		$show_modify_event = false;
		if(isset($_POST["modify_event_name"])){
			$show_modify_event = true;
			$show_validate_member = false;
		}
		if(isset($_POST['change_year_contribution'])){
			$show_not_in_order = true;
			$show_validate_member = false;
		}
		if(isset($_POST['show_not_in_ordre'])){
			$show_not_in_order = true;
			$show_validate_member = false;
		}elseif(isset($_POST['show_change_member_role'])){
			$show_change_role = true;
			$show_validate_member = false;
		}
		elseif(isset($_POST['show_create_contribution'])){
			$show_create_contribution = true;
			$show_validate_member = false;
		}elseif(isset($_POST['show_create_event'])){
			$show_create_event = true;
			$show_validate_member = false;
		}elseif(isset($_POST['show_modify_event'])){
			$show_modify_event = true;
			$show_validate_member = false;
		}

		$last_year = $this->_db->select_max_year_contribution();
		$all_events = $this->_db->select_all_events();
		$events = array();
		$passed_events = array();
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
		$last_event = $events[0];
		$notifications_new_contribution = array();
		$notifications_contributors = array();
		$notifications_change_member = "";
		$notifications_new_event = array();
		$notification_new_event_sucess = "";
		$notifications_update_event = array();
		$notification_update_event_sucess = "";
		$notification_picture_success = "";
		$notification_picture = "";

		if(!empty($_POST)){
			if(!empty($_POST['accepted'])){
				$accepted_member = $_POST['members_accepted'];
				for($i = 0; $i < count($accepted_member); $i++){
					$this->_db->accept_new_member($accepted_member[$i]);
					$this->_db->add_trainings(1, $accepted_member[$i]);
					$this->_db->insert_a_contributions($last_year, $accepted_member[$i]);
				}
			}


			//encode a new contribution
			if(!empty($_POST['new_contribution'])){
				if(empty($_POST['year'])){
					$notifications_new_contribution[] = "L'année est obligatoire !";
				}
				if(empty($_POST['price'])){
					$notifications_new_contribution[] = "Le prix est obligatoire !";
				}
				if(empty($notifications_new_contribution)){
					$year = $_POST['year'];
					$price = $_POST['price'];
					str_replace(',', '.', $price);
					if(!preg_match("/^[0-9]{4}$/", $year)){
						$notifications_new_contribution[] = "L'année doit être composé de 4 chiffre !";
					}
					if($year <= $this->_db->select_max_year_contribution()){
						$notifications_new_contribution[] = "L'année n'est pas une année ultérieur à l'année de la contribution courante !";
					}
					if(!preg_match("/^[0-9]+(\.[0-9]+)?$/", $price)){
						$notifications_new_contribution[] = "Le prix doit être un nombre avec ou sans virgule !";
					}
					if(empty($notifications_new_contribution)){
						$this->_db->insert_new_contribution($year, $price);
						$this->_db->update_all_contributors();
						$member = $this->_db->select_all_accepted_member();

						for($i = 0;$i < count($member); $i++){
							$this->_db->insert_a_contributions($year, $member[$i]->get_member_id());
							$mail_to_send = htmlspecialchars($member[$i]->get_mail());
							require_once('lib/PHPMailer/PHPMailer.php');
							require_once('lib/PHPMailer/SMTP.php');

							$mail = new PHPMailer(true);                       // true active les exceptions
							try {
								//Server settings
								$mail->SMTPDebug = 0;                          // Disable verbose debug output (=2 pour activer)
								$mail->isSMTP();                               // Set mailer to use SMTP
								$mail->Host = 'smtp.gmail.com';         	   // Specify main SMTP server
								$mail->SMTPAuth = true;                        // Enable SMTP authentication
								$mail->Username = 'le.groupe.un@gmail.com';             // SMTP username
								$mail->Password = 'groupe.un';           // SMTP password
								$mail->SMTPSecure = 'tls';                     // Enable TLS encryption, 'ssl' also accepted
								$mail->Port = 587;                             // TCP port to connect to

								//Recipients
								$mail->setFrom(EMAIL, 'site des coureur');
								$mail->addAddress($mail_to_send, $member[$i]->get_last_name());				// Add a recipient
								$mail->addReplyTo(htmlspecialchars(EMAIL), 'responsable') ;
								//$mail->addCC('cc@example.com');
								//$mail->addBCC('bcc@example.com');

								//Attachments
								//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
								//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

								//Content
								$mail->isHTML(true);                                  // Set email format to HTML
								$mail->Subject = 'Une nouvelle contribution a comencer .';
								$mail->Body    = "la contribution de $year a commencer.\n Le prix est de $price €.";
								//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

								$mail->send();
							} catch (Exception $e) {
								$notifications_new_contribution[]='Vos informations n\'ont pas été transmises. '.'Mailer Error: '.$mail->ErrorInfo;
							}
						}
					}
				}
				$show_create_contribution = true;
				$show_validate_member = false;

			}

			if(!empty($_POST['contributed'])){
				$last_year = $_POST['year_contributed'];
				if(empty($_POST['amount_paid'])){
					$notifications_contributors[] = "Aucun montant a été entrée !";
				}
				if(empty($notifications_contributors)){
					$members_contributed = $_POST['members_contributed'];
					$no_icomptatible_field = true;
					$no_empty_field = true;
					for($i = 0; $i < count($members_contributed); $i++){
						$member_id = $_POST['members_contributed'][$i];
						$amount_paid = $_POST['amount_paid'][$i];
						if(empty($amount_paid)){
							if($no_empty_field && $amount_paid != 0){
								$notifications_contributors[] = "Un ou plusieurs prix n'a pas été remplis !";
								$no_empty_field = false;
							}
						}
						elseif(!preg_match("/^[0-9]+(\.[0-9]+)?$/", $amount_paid)){
							if($no_icomptatible_field){
								$notifications_contributors[] = "Le prix doit être un nombre avec ou sans virgule ! Un ou plusieurs prix n'a pas été mis à jour !";
								$no_icomptatible_field = false;
							}
						}
						elseif($amount_paid < $this->_db->select_amount_paid($member_id, $_POST['year_contributed'])){
							$notifications_contributors = "$amount_paid est plus petit que " . $this->_db->select_amount_paid($member_id, $_POST['year_contributed']);
						}
						else{
							$this->_db->update_contributors($_POST['year_contributed'], $member_id, $amount_paid);
							if($_POST['year_contributed'] == $this->_db->select_max_year_contribution() && $amount_paid >= $this->_db->select_amount_paid($member_id, $_POST['year_contributed'])){
								$this->_db->update_contributed_member($member_id);
							}
							if($member_id == $_SESSION['info']->get_member_id()){
								$_SESSION['info'] = Db::getInstance()->select_member($member_id);
							}

						}
						$show_not_in_order = true;
						$show_validate_member = false;
					}
				}
			}

			if(!empty($_POST['change_year_contribution'])){
				$last_year = $_POST['year_contribution'];
			}

			if(!empty($_POST['change_resposability'])){
				$responsabilitys = $_POST['resposability'];
				$nb_admin = 0;
				for($i = 0 ; $i < count($responsabilitys) ; $i++){
					if(strcasecmp($responsabilitys[$i],"member") != 0){
						$nb_admin++;
					}
				}
				if($nb_admin < 1){
					$notifications_change_member = "Au moins un membre doit avoir le rôle de responsable !";
				}else{
					for($i = 0; $i < count($responsabilitys); $i++){
						$responsability = htmlspecialchars($responsabilitys[$i]);
						$member_id = $_POST['member_responsability'][$i];

						if(empty ($responsability)){
							$notification = "le champs est vide !";
						}
						else{
							$this->_db->update_responsability($member_id, $responsability);
							if($member_id == $_SESSION['info']->get_member_id()){
								if(strcasecmp($responsability,"member") != 0){
									$_SESSION['specialMembre'] = true;
									if(strcasecmp($responsability,"coach") == 0){
										$_SESSION['coach'] = true;
									}else{
										$_SESSION['coach'] = false;
									}
								}else{
									$_SESSION['specialMembre'] = false;
									$_SESSION['coach'] = false;
									header("Location: index.php?action=home");
									die();
								}
							}
						}
					}
				}
				$show_change_role = true;
				$show_validate_member = false;

			}

			if(!empty($_POST['change_modify_events'])){
					$last_event = $this->_db->select_event_by_event_name($_POST['modify_event_name']);
			}

			if(!empty($_POST['pay_participating_event'])){
				$members_have_paid = $_POST['member_have_paid'];
				$event_id = $_POST['num_have_paid_event'];
				for($i = 0; $i < count($members_have_paid); $i++){
					$member_id = $members_have_paid[$i];
					$this->_db->update_paied_participating_event($member_id, $event_id);
				}
				$last_event = $this->_db->select_event_id($event_id);
				$show_modify_event = true;
				$show_validate_member = false;
			}

			if(!empty($_POST['create_new_event'])){
				if(empty($_POST['start_date_new_event'])){
					$notifications_new_event[]="La date de début n'est pas indiqué !";
				}
				if(empty($_POST['end_date_new_event'])){
					$notifications_new_event[]="La date de fin n'est pas indiqué !";
				}
				if(empty($_POST['name_new_event'])){
					$notifications_new_event[] = "Le nom de l'évèment n'est pas indiqué !";
				}
				if(empty($_POST['location_new_event'])){
					$notifications_new_event[] = "Le lieu n'est pas indiqué !";
				}
				if(empty($_POST['cost_new_event'])){
					$notifications_new_event[] = "Le prix n'est pas indiqué !";
				}
				if(empty($_POST['description_new_event'])){
					$notifications_new_event[] = "Aucune description n'a été indiqué !";
				}
				if(empty($notifications_new_event)){
					$start_date_new_event = $_POST['start_date_new_event'];
					if(preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $start_date_new_event,$start_date)){
					$start_date_new_event = "$start_date[3]-$start_date[2]-$start_date[1]";
						if(strtotime($todays_date) > strtotime($start_date_new_event)){ //if on time
							$notifications_new_event[] = "La date de début est dépasser !";
						}
					}else{
						$notifications_new_event[] = "La date de début n'est pas au format jj/mm/yyyy";
					}
					$end_date_new_event = $_POST['end_date_new_event'];
					if(preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $end_date_new_event,$last_date)){
					$end_date_new_event = "$last_date[3]-$last_date[2]-$last_date[1]";
						if(strtotime($start_date_new_event) > strtotime($end_date_new_event)){ //if on time
							$notifications_new_event[] = "La date de fin précède celle de début !";
						}
					}else{
						$notifications_new_event['cost_new_event'] = "La date de fin n'est pas au format jj/mm/yyyy";
					}
					$cost = $_POST['cost_new_event'];
					$cost = str_replace(',', '.', $cost);
					if(!preg_match("/^[0-9]+(\.[0-9]+)?$/", $cost)){
						$notifications_new_event[] = "Le prix n'est pas au bon format !";
					}
					$latitude_new_event = null;
					if(!empty($_POST['lattitude_new_event'])){
						$latitude_new_event = $_POST['lattitude_new_event'];
						if(!preg_match("/^[0-9]+(\.[0-9]+)?$/", $latitude_new_event)){
							$notifications_new_event[] = "La latitude n'est pas au bon format !";
						}
					}
					$longitude_new_event = null;
					if(!empty($_POST['longitude_new_event'])){
						$longitude_new_event = $_POST['longitude_new_event'];
						if(!preg_match("/^[0-9]+(\.[0-9]+)?$/", $cost)){
							$notifications_new_event[] = "La longitude n'est pas au bon format !";
						}
					}
					$name_new_event = $_POST['name_new_event'];
					$location_new_event = htmlspecialchars($_POST['location_new_event']);
					$this->processForm();
					$description = $this->getRawHtml();
					$url_new_event = null;
					if(!empty($_POST['url_new_event'])){
						$url_new_event = htmlspecialchars($_POST['url_new_event']);
					}

					if(empty($notifications_new_event)){
						$this->_db->insert_new_invent($name_new_event, $location_new_event, $description, $url_new_event, $longitude_new_event, $latitude_new_event, $cost, $start_date_new_event, $end_date_new_event);
						$all_events = $this->_db->select_all_events();
						$events = array();
						foreach ($all_events as $indice => $event) { //recharge the event for including the new event.
							preg_match('/^(.*)\/(.*)\/(.*)$/', $event->get_date_start(),$result_starting);
							$event_date = "$result_starting[3]-$result_starting[2]-$result_starting[1]";
							if(strtotime($todays_date) < strtotime($event_date)){ //if on time
								$events[] = $event;
							}
						}
						$notification_new_event_sucess = "Évènement ajouter avec sucess !";
					}
					

				}
				$show_create_event = true;
				$show_validate_member = false;

			}

			if(!empty($_POST['update_event'])){
				if(empty($_POST['start_date_update_event'])){
					$notifications_update_event[]="La date de début n'est pas indiqué !";
				}
				if(empty($_POST['end_date_update_event'])){
					$notifications_update_event[]="La date de fin n'est pas indiqué !";
				}
				if(empty($_POST['name_update_event'])){
					$notifications_update_event[] = "Le nom de l'évèment n'est pas indiqué !";
				}
				if(empty($_POST['location_update_event'])){
					$notifications_update_event[] = "Le lieu n'est pas indiqué !";
				}
				if(empty($_POST['cost_update_event'])){
					$notifications_update_event[] = "Le prix n'est pas indiqué !";
				}
				if(empty($_POST['description_update_description'])){
					$notifications_update_event[] = "Aucune description n'a été indiqué !";
				}
				if(empty($notifications_update_event)){
					$start_date_update_event = $_POST['start_date_update_event'];
					if(preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $start_date_update_event,$start_date)){
					$start_date_update_event = "$start_date[3]-$start_date[2]-$start_date[1]";
						if(strtotime($todays_date) > strtotime($start_date_update_event)){ //if on time
							$notifications_update_event[] = "La date de début est dépasser !";
						}
					}else{
						$notifications_update_event[] = "La date de début n'est pas au format jj/mm/yyyy";
					}
					$end_date_update_event = $_POST['end_date_update_event'];
					if(preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $end_date_update_event,$last_date)){
					$end_date_update_event = "$last_date[3]-$last_date[2]-$last_date[1]";
						if(strtotime($start_date_update_event) > strtotime($end_date_update_event)){ //if on time
							$notifications_update_event[] = "La date de fin précède celle de début !";
						}
					}else{
						$notifications_update_event['cost_update_event'] = "La date de fin n'est pas au format jj/mm/yyyy";
					}
					$cost = $_POST['cost_update_event'];
					$cost = str_replace(',', '.', $cost);
					if(!preg_match("/^[0-9]+(\.[0-9]+)?$/", $cost)){
						$notifications_update_event[] = "Le prix n'est pas au bon format !";
					}
					$latitude_update_event = null;
					if(!empty($_POST['latitude_update_event'])){
						$latitude_update_event = $_POST['latitude_update_event'];
						if(!preg_match("/^[0-9]+(\.[0-9]+)?$/", $latitude_update_event)){
							$notifications_update_event[] = "La latitude n'est pas au bon format !";
						}
					}
					$longitude_update_event = null;
					if(!empty($_POST['longitude_update_event'])){
						$longitude_update_event = $_POST['longitude_update_event'];
						if(!preg_match("/^[0-9]+(\.[0-9]+)?$/", $cost)){
							$notifications_update_event[] = "La longitude n'est pas au bon format !";
						}
					}
					$event_id = $_POST['num_update_event'];
					$name_update_event = $_POST['name_update_event'];
					$location_update_event = $_POST['location_update_event'];
					$this->processFormUpdate();
					$description = $this->getRawHtml();
					$url_update_event = null;
					if(!empty($_POST['url_update_event'])){
						$url_update_event = $_POST['url_update_event'];
					}
					
					if(empty($notifications_update_event)){
						$this->_db->update_event($event_id, $name_update_event, $location_update_event, $description, $url_update_event, $longitude_update_event, $latitude_update_event, $cost, $start_date_update_event, $end_date_update_event);
						$all_events = $this->_db->select_all_events();
						$events = array();
						foreach ($all_events as $indice => $event) { //recharge the event for including the new event.
							preg_match('/^(.*)\/(.*)\/(.*)$/', $event->get_date_start(),$result_starting);
							$event_date = "$result_starting[3]-$result_starting[2]-$result_starting[1]";
							if(strtotime($todays_date) < strtotime($event_date)){ //if on time
								$events[] = $event;
							}
						}
						$notification_update_event_sucess = "Évènement modifier avec sucess !";
					}
					$last_event = $this->_db->select_event_id($event_id);
					$show_modify_event = true;
					$show_validate_member = false;

				}

			}

			//Add picture url
				if(!empty($_POST['add_url_picture'])){
					$url_picture = htmlspecialchars($_POST['pictures_url']);
					$event_id = $_POST['url_picture_event_id'];
					$this->_db->set_url_picture($url_picture, $event_id);
					$last_event = $this->_db->select_event_id($event_id);
					$show_modify_event = true;
					$show_validate_member = false;
					$notification_picture_success = "Url ajouter avec success";
				}
		}

		//research the table for all the operation we can do whith it.
		$new_member = $this->_db->select_new_members();
		$no_contributors = $this->_db->select_no_contributors($last_year);
		$have_paid = array();
		for($i = 0; $i < count($no_contributors); $i++){
			$member_id = $no_contributors[$i]->get_member_id();
			$have_paid[$member_id] = $this->_db->select_amount_paid($member_id, $last_year);
		}
		$years = $this->_db-> select_year_contribution();
		$price_to_pay = $this->_db->select_price_contribution($last_year);
		$members_accepted = $this->_db->select_all_accepted_member();
		$unpaid_member_event = $this->_db->select_unpaid_member($last_event->get_event_id());

		$table_interresed_members = Db::getInstance()->select_interrested_members_event_id($last_event->get_event_name());
		$table_participating_members = Db::getInstance()->select_participating_members_event_id($last_event->get_event_name());
		$this->processFormOldEvent($last_event);
		# Un contrôleur se termine en écrivant une vue
		require_once(VIEWS_PATH . 'admin.php');
	}

	//run() private methods
	private function mustProcessForm(){
		return isset($_POST['submit']);
	}

	private function processForm(){
		$this->_raw_html = $_POST['description_new_event'];
	}

	private function processFormOldEvent($last_event){
		$this->_raw_html = $last_event->get_description();
	}

	private function processFormUpdate(){
		$this->_raw_html = $_POST['description_update_description'];
	}

	/*private function renderView(){
		include(CHEMIN_VUES . 'wysiwyg.php');
	}*/


	// view private methods
	private function getRawHtml(){
		return $this->_raw_html;
	}

	private function getEscapedHtml(){
		return htmlentities($this->_raw_html);
	}

}
?>
