
	<h4>Bienvenue sur votre profil !</h4>
	<p><img src="<?php echo $member->get_picture() ?>" alt = "photo de profil" height="200" class="rounded"></p>

	<!-- SHOW THE STATE CURRENT STATE OF THE CONTRIBUTION -->
	<p>Etat actuel du paiement annuel :
	<?php if($_SESSION['info']->get_contributed() == 1){ ?>
		<span class="text-success"> Paiement à jour !  </span>
	<?php } else{ ?>
	  <span class="text-danger">Paiement en défaut ! </span>
	<?php } ?>
 </p>

	<!-- SHOW THE FORM FOR CHANGING THE PROFILE PICTURE -->
	<form class="form-check" method = "POST" action = "?action=account" enctype="multipart/form-data">
		<p><input type="hidden" name="MAX_FILE_SIZE" value="1000000"><br></p>
		<p class="text-success"> <strong> <?php echo $notifPicture ?> </strong> </p>
		<h5>Changer votre photo de profil :</h5>
		<p>Photo : <input type="file" name="profilpic"></p>
		<p><input class="btn btn-dark" type="submit" name="modifypicture" value="Modifier photo"></p>
		<br>
	</form>

	<!-- SHOW THE FORM FOR CHANGING THE PROFILE INFORMATIONS-->
	<form class="form-check" method = "POST" action = "?action=account">
		<p class="text-success"> <strong> <?php echo $notifProfil ?> </strong> </p>
		<h5>Modifier vos informations de compte : </h5>
		<p>Nom : <input type="text" name="lastname" value="<?php echo $member->get_last_name() ?>"> <strong class="text-danger"><?php echo $notifications['lastname'] ?></strong> </p>
		<p>Prenom : <input type="text" name="fistname" value="<?php echo $member->get_first_name() ?>"> <strong class="text-danger"><?php echo $notifications['firstname'] ?></strong> </p>
		<p>Mail : <input type="email" name="mail" value="<?php echo $member->get_mail() ?>"> <strong class="text-danger"><?php echo $notifications['mail'] ?></strong> </p>
		<p>Numéro de téléphonne : <input type="text" name="phoneNumber" value="<?php echo $member->get_phone_number() ?>"> <strong class="text-danger"><?php echo $notifications['phoneNumber'] ?></strong></p>
		<p>Numéro de compte : <input type="text" name="accountNumber" value="<?php echo $member->get_account_number() ?>"> <strong class="text-danger"><?php echo $notifications['accountNumber'] ?></strong></p>
		<p>Adresse : <input type="text" name="adress" value="<?php echo $member->get_adress() ?>"> <strong class="text-danger"><?php echo $notifications['adress'] ?></strong> </p>
		<p><input class="btn btn-dark" type="submit" name="modifyprofil" value="Modifier informations"></p>
	</form>

	<!-- SHOW THE FORM FOR CHANGING THE PASSWORD-->
	<form class="form-check" method = "POST" action = "?action=account">
		<p class="text-success"> <strong> <?php echo $notifPassword ?> </strong> </p>
		<h5>Changer votre mot de passe : </h5>
		<p>Ancien mot de passe : <input type="password" name="exPassword"> </p>
		<p>Nouveau mot de passe : <input type="password" name="newPassword1"> </p>
		<p>Nouveau mot de passe : <input type="password" name="newPassword2"> </p>
		<p><input class="btn btn-dark" type="submit" name="modifypassword" value="Modifier mot de passe"></p>
	</form>


	<!-- Show the table of the event that the member is participating into -->
	<div class="col-7">
		<h5>Liste des évenènements dont vous êtes participant :</h5>
		<table class="table table-bordered">
			<thead class="thead-light">
			   <tr>
			      <th class = "text-center" scope="col">Date</th>
			      <th scope="col">Nom de l'évenement</th>
		      	<th class = "text-center" scope="col">Description</th>
		      	<th class = "text-center" scope="col">Lieu</th>
					<th class = "text-center" scope="col">URL</th>
					<th class = "text-center" scope="col">Etat du paiment</th>
		    </tr>
		  </thead>
		  <tbody>
		    <?php foreach ($table_incoming_events as $indice => $event) { ?>
		    	<tr>
						<td> <?php echo $event->get_date_start()."<br><div class='text-center'> à </div>".$event->get_date_end(); ?> </td>
						<td> <?php echo $event->get_event_name() ?> </td>
						<td class = "text-center"> <?php echo $event->get_description() ?> </td>
						<td class = "text-center"> <?php echo $event->get_location() ?> </td>
						<td class = "text-center">
							<?php if($event->get_url() != null){ ?> <a href="<?php echo $event->get_url();?>"><?php echo $event->get_url();?></a> <?php }else{ echo "/";} ?>
						</td>
						<td class = "text-center">
							<?php if(in_array($event->get_event_id(), $table_paid_event)){?><p class="text-success"><?php echo "En ordre";}else{ ?>
							<p class="text-danger"><?php echo "En défaut";}?></p>
						</td>
					</tr>
		    <?php } ?>
		  </tbody>
		</table>
	</div>
	<br>
