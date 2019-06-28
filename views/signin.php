	<br>
	<h4>Inscription :</h4>
	<form enctype= "multipart/form-data" method = "POST" action = "?action=signin">
		<?php foreach($notifications as $i => $error){ ?>
			<p> <strong class="text-danger"> <?php echo $error ?> </strong> </p>
		<?php } ?>
		<p class="text-success"><?php echo $sended_sign_in ?></p>
		<div class="col-3">
			<p><input type="hidden" name="MAX_FILE_SIZE" value="1000000"></p>
			<p> Nom :  <input class="form-control" type="text" name="lastname" ></p>
			<p> Prénom :  <input class="form-control" type="text" name="firstname"></p>
			<p> Adresse :  <input class="form-control" type="text" name="adress"></p>
			<p> Numéro de téléphonne:  <input class="form-control" type="text" name="number"></p>
			<p> Mail :  <input class="form-control" type="email" name="mail"></p>
			<p> Numéro de compte  :  <input class="form-control" type="text" name="account_number"></p>
			<p> Mot de passe : <input class="form-control" type="password" name="password"></p>
			<p> Photo de profil :  <input type="file" name="profilpic"></p>
			<input class="btn btn-dark" type="submit" name="create" value="S'inscrire">
		</div>
	</form>
