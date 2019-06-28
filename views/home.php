		<img src="<?php echo TEAM_PICTURE ?>"  class="img-fluid rounded float-right" alt="photo de groupe">
		<h4>Bienvenue sur la page d'accueil de notre site !</h4>
		<br>
		<p class="ml-3">
			Bienvenue sur le site officiel des coureurs du <?php echo GROUP_NAME ?>
			de la ville de <?php echo CITY_NAME ?> en Belgique.<br>
			Ce site est réservé aux membres du club; il permet la gestion de ses membres (cotisation),
			des évenements ainsi que les plans d'entrainement.
		</p>
		<?php if(empty($_SESSION['authentifie'])){ ?>
			<form method = "POST" action = "?action=accueil" >
				<p> <strong> <?php echo $notification ?> </strong> </p>
				<div class="col-3">
					<p> Mail :  <input class="form-control" type="text" name="username" ></p>
					<p>Mot de passe :  <input class="form-control" type="password" name="password"></p>
					<input class="btn btn-dark" type="submit" name="connecting" value="Se connecter">
				</div>
			</form>

			<form class="col-3">
				<br>
				<p>Pas de compte ? <input class="btn btn-dark" type="button" value="Inscrivez-vous" onclick="window.location.href= 'index.php?action=signin'"/><p>
				<p class ="text-success"> <?php echo INVIT_MESSAGE; ?> <p>
			</form>

		<?php } else{?>
			<!-- pour regler un petit soucis au niveau du footer -->
			<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
		<?php } ?>
