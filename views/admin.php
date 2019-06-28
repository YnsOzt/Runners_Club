<h4>Bienvenue sur la page d'administrateur</h4>

<form class="form-check" action="?action=admin" method="post">
	<input type="submit" class="btn btn-dark" value="Afficher les membres à valider" name="show_validate_member">
	<input type="submit" class="btn btn-dark" value="Afficher les membres pas en ordre" name="show_not_in_ordre">
	<input type="submit" class="btn btn-dark" value="Changer le rôle des membres" name="show_change_member_role">
	<input type="submit" class="btn btn-dark" value="Crée cotisation" name="show_create_contribution">
	<input type="submit" class="btn btn-dark" value="Crée évenement" name="show_create_event">
	<input type="submit" class="btn btn-dark" value="Voir évenement" name="show_modify_event">
</form>
<br>
<?php if($show_validate_member == true){ ?>
<h5>membre inscrit à valider</h5>

	<form class="col-6" action="?action=admin" method="post">
			<table class = "table table-bordered">
				<thead class="thead-light">
					<tr>
						<th class = "text-center">photo de profil</th>
						<th class = "text-center">Nom</th>
						<th class = "text-center">Prénom</th>
						<th class = "text-center">Mail</th>
						<th class = "text-center">Adresse</th>
						<th class = "text-center">n° téléphone</th>
						<th class = "text-center">n° compte en banque</th>
						<th class = "text-center"><input class="btn btn-dark" type="submit" name="accepted" value="Accepter"></th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0;$i<count($new_member);$i++) { ?>
						<tr>
							<td class = "text-center"><img src="<?php echo $new_member[$i]->get_picture() ?>" alt = "photo de profil" height=150 class="rounded"></td>
							<td class = "text-center"><?php echo htmlspecialchars($new_member[$i]->get_last_name()) ?></td>
							<td class = "text-center"><?php echo htmlspecialchars($new_member[$i]->get_first_name()) ?></td>
							<td class = "text-center"><?php echo htmlspecialchars($new_member[$i]->get_mail()) ?></td>
							<td class = "text-center"><?php echo htmlspecialchars($new_member[$i]->get_adress()) ?></td>
							<td class = "text-center"><?php echo htmlspecialchars($new_member[$i]->get_phone_number()) ?></td>
							<td class = "text-center"><?php echo htmlspecialchars($new_member[$i]->get_account_number()) ?></td>
							<td class = "text-center"><input type="checkbox" name="members_accepted[]" value="<?php echo $new_member[$i]->get_member_id() ?>"></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</form>
	<br>
<?php } ?>
<?php if($show_create_contribution == true) {?>
	<h5>Cotisation</h5>
	<?php foreach($notifications_new_contribution as $i => $error){ ?>
			<p> <strong class="text-danger"> <?php echo $error ?> </strong> </p>
		<?php } ?>
	<form action="?action=admin" method="post">
	<h6>Commencer une nouvelle année de contribution.</h6
	<p>année de la nouvelle cotisation : <input type="text" name="year"></p>
	<p>prix : <input type="text" name="price"></p>
	<p><input class="btn btn-dark" type="submit" name="new_contribution" value="Commencer une nouvelle cotisation"></p>
	</form>
	<br>
<?php } ?>

<?php if($show_not_in_order == true) {?>
	<h5>Membre qui n'ont pas encore payé leur cotistion</h5>

	<?php foreach($notifications_contributors as $i => $error){ ?>
			<p> <strong class="text-danger"> <?php echo $error ?> </strong> </p>
		<?php } ?>

	<form class="col-8" action="?action=admin" method="post">
		<input type = "hidden" name="year_contributed" value="<?php echo $last_year ?>">
		<p>Montant de <?php echo $last_year ?> : <?php echo $price_to_pay ?> €.</p>
			<table class = "table table-bordered">
				<thead class="thead-light">
					<tr>
						<th class = "text-center">photo de profil</th>
						<th class = "text-center">Nom</th>
						<th class = "text-center">Prénom</th>
						<th class = "text-center">n° compte en banque</th>
						<th class = "text-center"><input class="btn btn-dark" type="submit" name="contributed" value="A payer"></th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0;$i<count($no_contributors);$i++) { ?>
						<tr>
							<td class = "text-center"><img src="<?php echo $no_contributors[$i]->get_picture() ?>" alt = "photo de profil" height=150 class="rounded"></td>
							<td class = "text-center"><?php echo htmlspecialchars($no_contributors[$i]->get_last_name()) ?></td>
							<td class = "text-center"><?php echo htmlspecialchars($no_contributors[$i]->get_first_name()) ?></td>
							<td class = "text-center"><?php echo htmlspecialchars($no_contributors[$i]->get_account_number()) ?></td>
							<td class = "text-center"><input type="text" name="amount_paid[]" value = "<?php echo $have_paid[$no_contributors[$i]->get_member_id()] ?>"></td>
							<input type="hidden" name="members_contributed[]" value = "<?php echo $no_contributors[$i]->get_member_id()?>">
						</tr>
					<?php } ?>
				</tbody>
			</table>

		<p>Changer l'année de cotisation : <select name = "year_contribution">
											<?php for ($i=0;$i<count($years);$i++){ ?>
											<option <?php if($last_year == $years[$i]){echo 'selected = "selected"';} ?>><?php echo $years[$i] ?></option>
											<?php } ?>
											</select> <input class="btn btn-dark"  type="submit" name="change_year_contribution" value = "charger les cotisation de cette année"></p>
		</form>
	<?php } ?>


<?php if($show_change_role == true){ ?>
	<h5>Changer le rôle des membre</h5>

		<p> <strong class="text-danger"> <?php echo $notifications_change_member ?> </strong> </p>

	<form class="col-8" action="?action=admin" method="post">
		<table class = "table table-bordered">
				<thead class="thead-light">
					<tr>
						<th class = "text-center">photo de profil</th>
						<th class = "text-center">Nom</th>
						<th class = "text-center">Prénom</th>
						<th class = "text-center"><input class="btn btn-dark" type="submit" name="change_resposability" value="Changer le rôle"></th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0;$i<count($members_accepted);$i++) { ?>
						<tr>
							<td class = "text-center"><img src="<?php echo $members_accepted[$i]->get_picture() ?>" alt = "photo de profil" height=150 class="rounded"></td>
							<td class = "text-center"><?php echo htmlspecialchars($members_accepted[$i]->get_last_name()) ?></td>
							<td class = "text-center"><?php echo htmlspecialchars($members_accepted[$i]->get_first_name()) ?></td>
							<td class = "text-center"><input type="text" name="resposability[]" value = "<?php echo $members_accepted[$i]->get_responsability() ?>"></td>
							<input type="hidden" name="member_responsability[]" value = "<?php echo $members_accepted[$i]->get_member_id() ?>">
						</tr>
					<?php } ?>
				</tbody>
			</table>
	</form>
<?php } ?>

<?php if($show_create_event == true){ ?>
	<h6>Créer un nouvel évènement</h6>

 	<?php if($notification_new_event_sucess != ""){ ?><p class = "text-success"><?php echo $notification_new_event_sucess ?></p><?php } ?>

	<?php foreach($notifications_new_event as $i => $error){ ?>
			<p> <strong class="text-danger"> <?php echo $error ?> </strong> </p>
		<?php } ?>

	<form action = "?action=admin" method = "post">
		<p>Date de début : <input type="text" name= "start_date_new_event" placeholder="JJ/MM/AAAA"> Date de fin : <input type="text" name= "end_date_new_event" placeholder="JJ/MM/AAAA"></p>
		<p>Nom de l'évènement : <input type="text" name = "name_new_event"></p>
		<p>description : <textarea id="editor" name="description_new_event"></textarea></p>
		<p>Lieu : <input type="text" name = "location_new_event"></p>
		<p>url de l'évènement (pas obligatoire) : <input type = "text" name = "url_new_event"><p>
		<p>coût : <input type = "text" name = "cost_new_event"></p>
		<p>latitude (pas obligatoire) : <input type = "text" name = "lattitude_new_event"> longitude (pas obligatoire) : <input type = "text" name = "longitude_new_event"</p>
		<input class="btn btn-dark" type = "submit" name = "create_new_event" value = "Créer un évènement">
		</form>

		<br>
<?php } ?>

<?php if($show_modify_event == true){ ?>
		<h6>Changer d'évènement</h6>

		<form action="?action=admin" method="post">
	<p> choisissez l'évènement : <select name = "modify_event_name">
											<?php for ($i=0;$i<count($all_events);$i++){ ?>
											<option <?php if($last_event == $all_events[$i]){echo 'selected = "selected"';} ?>><?php echo $all_events[$i]->get_event_name() ?></option>
											<?php } ?>
											</select> <input class="btn btn-dark"  type="submit" name="change_modify_events" value = "changer l'évènement"></p>
	</form>

		<h5>Liste des interrésées :</h5>
			<div class="col-5">
				<table class="table table-bordered">
				 <thead class="thead-light">
					  <tr>
							<th class = "text-center" scope="col">Photo</th>
					  	<th class = "text-center" scope="col">Nom</th>
					  	<th class="text-center" scope="col">Prénom</th>
						<th class="text-center" scope="col">Email</th>
					  </tr>
					</thead>
					<tbody>
					  <?php foreach ($table_interresed_members as $indice => $member) { ?>
					  <tr>
							<td class = "text-center"><img src="<?php echo $member->get_picture() ?>" alt = "photo de profil" height=100px class="rounded"> </td>
							<td class = "text-center"> <?php echo htmlspecialchars($member->get_last_name()) ?> </td>
							<td class = "text-center"> <?php echo htmlspecialchars($member->get_first_name()) ?> </td>
							<td class = "text-center"> <?php echo htmlspecialchars($member->get_mail()) ?> </td>
						</tr>
					  <?php } ?>
					</tbody>
				</table>
			</div>

			<h5>Liste des participant :</h5>
			<div class="col-5">
				<table class="table table-bordered">
				 <thead class="thead-light">
					  <tr>
							<th class = "text-center" scope="col">Photo</th>
					  	<th class = "text-center" scope="col">Nom</th>
					  	<th class="text-center" scope="col">Prénom</th>
						<th class="text-center" scope="col">Mail</th>
					  </tr>
					</thead>
					<tbody>
					  <?php foreach ($table_participating_members as $indice => $member) { ?>
					  <tr>
							<td class = "text-center"><img src="<?php echo $member->get_picture() ?>" alt = "photo de profil" height=100 class="rounded"> </td>
							<td class = "text-center"> <?php echo htmlspecialchars($member->get_last_name()) ?> </td>
							<td class = "text-center"> <?php echo htmlspecialchars($member->get_first_name()) ?> </td>
							<td class = "text-center"> <?php echo htmlspecialchars($member->get_mail()) ?> </td>
						</tr>
					  <?php } ?>
					</tbody>
				</table>
			</div>
	<?php if(in_array($last_event,$events)){ ?>
	<h5>Modifier l'évènement.</h5>

	<?php if($notification_update_event_sucess != ""){ ?><p class = "text-success"><?php echo $notification_update_event_sucess ?></p><?php } ?>

	<?php foreach($notifications_update_event as $i => $error){ ?>
			<p> <strong class="text-danger"> <?php echo $error ?> </strong> </p>
		<?php } ?>

	<form action = "?action=admin" method = "post">
		<input type="hidden" name = "num_update_event" value = "<?php echo $last_event->get_event_id()?>">
		<p>Date de début : <input type="text" name= "start_date_update_event" value = "<?php echo $last_event->get_date_start()	?>"> Date de fin : <input type="text" name= "end_date_update_event" value = "<?php echo $last_event->get_date_end()	?>"></p>
		<p>Nom de l'évènement : <input type="text" name = "name_update_event" value = "<?php echo $last_event->get_event_name()	?>"></p>
		<p>description : <textarea id="editor" name="description_update_description"><?php echo $this->getRawHtml(); ?></textarea></p>
		<p>Lieu : <input type="text" name = "location_update_event" value = "<?php echo $last_event->get_location()?>"></p>
		<p>url de l'évènement (pas obligatoire) : <input type = "text" name = "url_update_event" value = "<?php if($last_event->get_url() != null){echo $last_event->get_url();}?>"><p>
		<p>coût : <input type = "text" name = "cost_update_event" value = "<?php echo $last_event->get_cost()?>"></p>
		<p>latitude (pas obligatoire) : <input type = "text" name = "latitude_update_event" value = "<?php if($last_event->get_latitude() != null){echo $last_event->get_latitude();}?>"> longitude (pas obligatoire) : <input type = "text" name = "longitude_update_event" value = "<?php if($last_event->get_longitude() != null){echo $last_event->get_longitude();}?>"></p>
		<input class="btn btn-dark" type = "submit" name = "update_event" value = "Modifier un évènement">
		</form>

	<h5>Valider un paiement de participation à l'évènement.</h5>

	<form class = "col-8" action = "?action=admin" method = "post">
		<input type = "hidden" name = "num_have_paid_event" value = "<?php echo $last_event->get_event_id() ?>">
		<p>Montant de <?php echo $last_event->get_event_name() ?> : <?php echo $last_event->get_cost() ?> €.</p>
			<table class = "table table-bordered">
				<thead>
					<tr>
						<th class = "text-center">photo de profil</th>
						<th class = "text-center">Nom</th>
						<th class = "text-center">Prénom</th>
						<th class = "text-center">n° compte en banque</th>
						<th class = "text-center"><input class="btn btn-dark" type="submit" name="pay_participating_event" value="A payer"></th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0;$i<count($unpaid_member_event);$i++) { ?>
						<tr>
							<td class = "text-center"><img src="<?php echo $unpaid_member_event[$i]->get_picture() ?>" alt = "photo de profil" height=150px class="rounded"></td>
							<td class = "text-center"><?php echo htmlspecialchars($unpaid_member_event[$i]->get_last_name()) ?></td>
							<td class = "text-center"><?php echo htmlspecialchars($unpaid_member_event[$i]->get_first_name()) ?></td>
							<td class = "text-center"><?php echo htmlspecialchars($unpaid_member_event[$i]->get_account_number()) ?></td>
							<td class = "text-center"><input type="checkbox" name="member_have_paid[]" value = "<?php echo $unpaid_member_event[$i]->get_member_id() ?>"></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
	</form>
<?php }else{ ?>
	<h5>Ajouter un url des photos.</h5>
	<?php if(!empty($notification_picture_success)){ ?> <p class="text-success"> <?php echo $notification_picture_success ?> </p> <?php } ?>
	<?php if(!empty($notification_picture)){ ?> <p class="text-danger"> <?php echo $notification_picture ?> </p> <?php } ?>
	<form action="?action=admin" method="post">
		<input type="hidden" name="url_picture_event_id" value="<?php echo $last_event->get_event_id() ?>">
	<p>url des photos : <input type="text" name="pictures_url" <?php if($last_event->get_url_picture() != null){ ?> value="<?php echo $last_event->get_url_picture() ?>" <?php } ?> >
		<input class="btn btn-dark" type="submit" name="add_url_picture" value="Ajouter un lien pour les photos" >
	</form>
<?php } ?>
<?php } ?>

<script script src="./assets/ckeditor.js">
</script>
<script src="./views/javascript/wysiwyg.js">
</script>
