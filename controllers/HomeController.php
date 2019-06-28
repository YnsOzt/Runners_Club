<?php
class HomeController{

	public function __construct() {

	}

	public function run(){

		//if someone try to connect
		$notification = "Identifiez-vous";
		if(isset($_POST['connecting'])){
			if(empty($_POST['username']) && empty($_POST['password'])){
				$notification = "Vous n'avez pas entrez de mail et de mot de passe";
			}
			elseif(empty($_POST['username'])){
				$notification = "Vous n'avez pas entrez de mail";
			}
			elseif(empty($_POST['password'])){
				$notification = "Vous n'avez pas entrez de mot de passe";
			}
			else{
				$member = Db::getInstance()->validate_member(htmlspecialchars($_POST['username']), htmlspecialchars($_POST['password']));
				if($member != null){
					if($member->get_accepted() == 0){
						$notification = "Votre inscription n'est pas encore valider, un admin va s'en charger !";
					}else{
						$_SESSION['authentifie'] = true;
						$_SESSION['info'] = $member;
						if(strcasecmp($member->get_responsability(),"member") != 0){
							$_SESSION['specialMembre'] = true;
							if(strcasecmp($member->get_responsability(),"coach") == 0){
								$_SESSION['coach'] = true;
							}else{
								$_SESSION['coach'] = false;
							}
						}else{
							$_SESSION['specialMembre'] = false;
							$_SESSION['coach'] = false;
						}
						header("Location: index.php?action=plan");
						die();
					}
				}else{
					$notification = "Vous n'avez pas entrer le bon compte";
				}
			}
		}

		require_once(VIEWS_PATH . 'home.php');
	}

}
?>
