<?php
class SignInController{

	public function __construct() {

	}

	public function run(){
		$notifications = array();
		$sended_sign_in = "";

		if(!empty($_POST)){
			$db = Db::getInstance();
			$accepted_sign_in = true;
			if(empty($_POST['lastname'])){

				$notifications[] = "Le nom est obligatoire !"; //there is no last name.
				$accepted_sign_in = false;
			}
			if(empty($_POST['firstname'])){
				$notifications[] = "Le prénom est obligatiore !"; //there is no first name.
				$accepted_sign_in = false;
			}
			if(empty($_POST['mail'])){
				$notifications[] = "Le mail est obligatoire !"; //there is no mail.
				$accepted_sign_in = false;
			}
			if(empty($_POST['password'])){
				$notifications[] = "Le mot de passe est obligatoire !"; //there is no password.
				$accepted_sign_in = false;
			}
			if(empty($_POST['adress'])){
				$notifications[] = "L'adresse est obligatoire ! ";//there is no adress.
				$accepted_sign_in = false;
			}
			if(empty($_POST['number'])){
				$notifications[] = "Le numero de téléphone est obligatoire !";//there is no phone number.
				$accepted_sign_in = false;
			}
			if(empty($_POST['account_number'])){
				$notifications[] = "Le numero de compte en banque est obligatoire !";//threr is no account number.
				$accepted_sign_in = false;
			}

			$number = $_POST['number'];
			if(!preg_match('/^[0-9]{7,11}$/', $number)){
				$notifications[] = "Le numero de téléphone ne doit contenir que des chiffres ! ".$number." n'est pas accepté."; //the phone number have other character that number.
				$accepted_sign_in = false;
			}

			$mail = $_POST['mail'];
			if(!preg_match('/^([a-z0-9A-Z]+(([\.\-\_]?[a-z0-9A-Z]+)+)?)\@(([a-zA-Z0-9]+[\.\-\_])+[a-zA-Z]{2,4})$/', $mail)){

			$notifications[] = "L'adresse mail n'est pas valable ! ".$mail." n'est pas accepté."; //the phone number have other character that number.
			$accepted_sign_in = false;
			}

			$account = $_POST['account_number'];
			if(!preg_match('/^[A-Z]{2}[0-9]{14}$/i', $account)){

			$notifications[] = "Le numero de compte en banque n'est pas valable !"; //the account number is not in the correct form.
			$accepted_sign_in = false;
			}

			$lastname = $_POST['lastname'];
			$firstname = $_POST['firstname'];
			$adress = $_POST['adress'];
			$password = $_POST['password'];
			$profil_pic;
			if($accepted_sign_in){ //it cannot insert into the db if all of the field are not correct.
				$imageInfo = @getimagesize($_FILES['profilpic']['tmp_name']);
				if($imageInfo['mime'] != "image/jpeg" && $imageInfo['mime'] != "image/png"){
				$notifications[] = "Le fichier doit être de type jpeg ou pgn";
			}else{
					$imageinfo = getimagesize($_FILES['profilpic']['tmp_name']);
					if (($_FILES['profilpic']['type']=='image/jpeg' && $imageinfo['mime']=='image/jpeg') || ($_FILES['profilpic']['type']=='image/png' && $imageinfo['mime']=='image/png')) {
						$horodatage=str_replace('.', '_',microtime(true));
						$origine = $_FILES['profilpic']['tmp_name'];
						$profil_pic = VIEWS_PATH . 'images/' . $horodatage . basename($_FILES['profilpic']['name']);
						move_uploaded_file($origine,$profil_pic);
						$db->insert_members($lastname, $firstname, $adress, $mail,password_hash($_POST['password'], PASSWORD_BCRYPT), $number, $account, $profil_pic);
						$sended_sign_in = "L'inscription a été envoyé, un admin doit encore accepté votre inscription pour pouvoir accéder au site.";
					}
				}
			}
		}

		require_once(VIEWS_PATH . 'signin.php');
	}

}
?>
