<?php
class AccountController{

	public function __construct() {

	}

	public function run(){
		if(empty($_SESSION['authentifie'])){
				header("Location: ?action=accueil");
				die();
		}
		$member = $_SESSION['info'];


		//the case that someone clicked on 'modifier informations' to change his account info
		$notifProfil="";
		$notifications = array(
			"lastname" =>"",
			"firstname" => "",
			"mail" => "",
			"adress" => "",
			"phoneNumber" => "",
			"accountNumber" => "",
		);
		$modification_accepted = true;
		if(isset($_POST['modifyprofil'])){
			if(empty($_POST['lastname'])){
				$notifications['lastname'] = "Le nom est obligatoire !"; //there is no last name.
				$modification_accepted = false;
			}
			if(empty($_POST['fistname'])){
				$notifications['fistname'] = "Le prénom est obligatiore !"; //there is no first name.
				$modification_accepted = false;
			}
			if(empty($_POST['mail'])){
				$notifications['mail'] = "Le mail est obligatoire !"; //there is no mail.
				$modification_accepted = false;
			}
			if(empty($_POST['adress'])){
				$notifications['adress'] = "L'adresse est obligatoire ! ";//there is no adress.
				$modification_accepted = false;
			}
			if(empty($_POST['phoneNumber'])){
				$notifications['phoneNumber'] = "Le numero de téléphone est obligatoire !";//there is no phone number.
				$modification_accepted = false;
			}
			if(empty($_POST['accountNumber'])){
				$notifications['accountNumber'] = "Le numero de compte en banque est obligatoire !";//threr is no account number.
				$modification_accepted = false;
			}
			if(!preg_match('/^[0-9]{7,11}$/', $_POST['phoneNumber'])){
				$notifications['PhoneNumber'] = "Le numero de téléphone ne doit contenir que des chiffres ! ".$_POST['phoneNumber']." n'est pas accepté."; //the phone number have other character that number.
				$modification_accepted = false;
			}
			if(!preg_match('/^([a-z0-9A-Z]+(([\.\-\_]?[a-z0-9A-Z]+)+)?)\@(([a-zA-Z0-9]+[\.\-\_])+[a-zA-Z]{2,4})$/', $_POST['mail'])){
				$notifications['mail'] = "L'adresse mail n'est pas valable ! ".$_POST['mail']." n'est pas accepté."; //the phone number have other character that number.
				$modification_accepted = false;
			}
			if(!preg_match('/^[A-Z]{2}[0-9]{14}$/i', $_POST['accountNumber'])){
				$notifications['accountNumber'] = "Le numero de compte en banque n'est pas valable !"; //the account number is not in the correct form.
				$modification_accepted = false;
			}




			if($modification_accepted){
				Db::getInstance()->update_member_info($member->get_member_id(), htmlspecialchars($_POST['lastname']),
																	htmlspecialchars($_POST['fistname']), htmlspecialchars($_POST['mail'])
			 														,htmlspecialchars($_POST['adress']),  htmlspecialchars($_POST['phoneNumber'])
																	, htmlspecialchars($_POST['accountNumber']));

					//UPDATE OF THE VARIABLES TO SHOW THE UPDATE INSTANLY
				$_SESSION['info'] = Db::getInstance()->select_member($member->get_member_id());
				$member = $_SESSION['info'];
				$notifProfil="Les informations de votre compte ont bien été changés";
			}

		}



		//change the profile PICTURE
		$notifPicture = "";
		if(isset($_POST['modifypicture'])){
			$imageInfo = getimagesize($_FILES['profilpic']['tmp_name']);
			if($imageInfo['mime'] != "image/jpeg" && $imageInfo['mime'] != "image/png"){
				$notifPicture = "Le fichier doit être de type jpeg ou pgn";
			}else{
				$horotodage = microtime();
				@unlink($member->get_picture()); //delete the previous picture
				move_uploaded_file($_FILES['profilpic']['tmp_name'],'views/images/'.$horotodage.basename($_FILES['profilpic']['name']));
				$photo = 'views/images/'.$horotodage.basename($_FILES['profilpic']['name']);
				Db::getInstance()->update_profile_picture($member->get_member_id(),$photo);


				//UPDATE OF THE VARIABLES TO SHOW THE UPDATE INSTANLY
				$_SESSION['info'] = Db::getInstance()->select_member($member->get_member_id());
				$member = $_SESSION['info'];
				$notifPicture = "Votre photo de profil a été changé";
			}
		}



		//change the password
		$notifPassword = "";
		if(isset($_POST['modifypassword'])){
			if(!empty($_POST['exPassword']) && !empty($_POST['newPassword1']) && !empty($_POST['newPassword2'])){
				if(!Db::getInstance()->validate_member($member->get_mail(), htmlspecialchars($_POST['exPassword']))){
					$notifPassword = "Votre ancien mot de passe n'est pas correct";
				}
				elseif(htmlspecialchars($_POST['newPassword1']) != htmlspecialchars($_POST['newPassword2'])){
					$notifPassword = "Le nouveau mot de passe n'est pas encodé deux fois correctement";
				}
				else{
					$notifPassword = "Votre mot de passe a bien été changé";
					$hash = password_hash(htmlspecialchars($_POST['newPassword1']),PASSWORD_BCRYPT);
					Db::getInstance()->update_password($member->get_member_id(),$hash);
				}

			}else{
				$notifPassword = "Veuillez compléter chaque champs";
			}
		}

		//table of participating event
		$all_participated_events = Db::getInstance()->select_acutal_members_event();
		$table_incoming_events = array();
		preg_match('/^(.*)\/(.*)\/(.*)$/', DATE,$result_today);
		$todays_date = "$result_today[3]-$result_today[2]-$result_today[1]";
		foreach ($all_participated_events as $indice => $event) {
			preg_match('/^(.*)\/(.*)\/(.*)$/', $event->get_date_start(),$result_starting);
			$event_date = "$result_starting[3]-$result_starting[2]-$result_starting[1]";
			if(strtotime($todays_date) <= strtotime($event_date)){ //if on time
				$table_incoming_events[] = $event;
			}
		}
		//table of paid participating events
		$table_paid_event = Db::getInstance()->select_paid_participating_events($member->get_member_id());

		require_once(VIEWS_PATH . 'account.php');
	}



}
?>
