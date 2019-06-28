	<h4>Bienvenue sur la page d'évenement</h4><br>


	<!--SHOW THE TABLE WITH ALL THE EVENT -->
	<p class="text-success"> <strong><?php echo $notif_payment ?></strong></p>
	<p class="text-success"> <strong><?php echo $notif_interest ?></strong></p>
	<h5>Voici la liste des différentes évènements : </h5>
	<form class ="col-12" action="?action=event" method="post">
			<table class="table table-bordered">
		  <thead class="thead-light">
		    <tr>
		      <th class = "text-center" scope="col">Date</th>
		      <th scope="col">Nom de l'évenement</th>
		      <th class = "text-center" scope="col">Description</th>
		      <th class = "text-center" scope="col">Lieu</th>
					<th class = "text-center" scope="col">URL</th>
					<th class = "text-center" scope="col">Côut</th>
					<th scope="col"><input type="submit" name="interrested" value="Interesser"></th>
					<th scope="col"><input type="submit" name="participating" value="Participer"></th>
					<th scope="col"><input type="submit" name="map" value="MAP"></th>
		    </tr>
		  </thead>
		  <tbody>
		    <?php foreach ($events as $indice => $event) { ?>
		    	<tr>
						<td> <?php echo $event->get_date_start()."<br><div class='text-center'> à </div>".$event->get_date_end(); ?> </td>
						<td> <?php echo $event->get_event_name() ?> </td>
						<td class = "text-center"> <?php echo $event->get_description() ?> </td>
						<td class = "text-center"> <?php echo $event->get_location() ?> </td>
						<td class = "text-center">
							<?php if($event->get_url() != null){ ?> <a href="<?php echo $event->get_url();?>"><?php echo $event->get_url();?></a> <?php }else{ echo "/";} ?>
						</td>
						<td class = "text-center"> <?php echo $event->get_cost().'€' ?> </td>
						<td class = "text-center">
							<input type="checkbox" name="interrestedlist[]" value="<?php echo $event->get_event_id() ?>"
							<?php if(in_array($event->get_event_id(),$interrested_events)){?> checked = "checked" <?php } ?>>
						</td>
						<td class = "text-center">
							<input type="checkbox" name="participatinglist[]" value="<?php echo $event->get_event_id() ?>"
							<?php if(in_array($event->get_event_id(),$participating_events)){?> checked = "checked" <?php } ?> >
						</td>
						<td class = "text-center">
							<input <?php if($event->get_latitude() == null || $event->get_longitude() == null){ ?> disabled<?php } ?>
										 type="radio" name="maplocation" value="<?php echo $event->get_event_id() ?>">
						</td>
					</tr>
		    <?php } ?>
		  </tbody>
		</table>
	</form>
	<br>

	<!--SHOW THE GOOGLE MAP-->
	<?php if($show_map != false){ ?>
		<h5> Voici la localisation de l'évènement <strong>'<?php echo $localisation_event_name ?>' </strong> :  </h5>
		<div id="map"> </div>
	  <script>
	      function initMap() {
	          var position = {lat: <?php echo $lat; ?>, lng: <?php echo $long; ?>};
	          var map = new google.maps.Map(document.getElementById('map'), {
	          zoom: 18,
	          center: position
	          });
	          var marker = new google.maps.Marker({
	              position: position,
	              map: map
	          });
	      }
	  </script>
	  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDw7AnqVtGTXp2LLx1XmF5yN2g7J_Bcng&callback=initMap"></script>
		<br>
	<?php } ?>




	<!--SHOW THE TABLE WITH THE PARTICIPATING AND INTERRESTED MEMBERS OF THE EVENT OF CHOICE -->
	<h5>Choisissez le nom de l'évènement pour voir ses intérêts et participations :</h5>
	<form action="?action=event" method="post">
		<div class="form-group col-3">
	  <label>Choisissez le nom de votre évènement :</label>
	  <select class="form-control" name="nameofevent">
	    <?php foreach($events as $indice => $event) { ?>
				<option <?php if($event->get_event_name() == $last_event){ ?> selected = "selected" <?php } ?> >
					<?php echo $event->get_event_name(); ?>
				</option>
			<?php } ?>
	  </select>
		<br>
		<input type="submit" class="btn btn-dark" name="search" value="Rechercher">
		</div>
	</form>
	<?php if($checked != false) {?>
		<h5>Liste des interrésées :</h5>
		<div class="col-5">
			<table class="table table-bordered">
			 <thead class="thead-light">
				  <tr>
						<th class = "text-center" scope="col">Photo</th>
				  	<th class = "text-center" scope="col">Nom</th>
				  	<th class="text-center" scope="col">Prénom</th>
				  </tr>
				</thead>
				<tbody>
				  <?php foreach ($table_interresed_members as $indice => $member) { ?>
				  <tr>
						<td class = "text-center"><img src="<?php echo $member->get_picture() ?>" alt = "photo de profil" height=100px class="rounded"> </td>
						<td class = "text-center"> <?php echo $member->get_last_name() ?> </td>
						<td class = "text-center"> <?php echo $member->get_first_name() ?> </td>
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
				  </tr>
				</thead>
				<tbody>
				  <?php foreach ($table_participating_members as $indice => $member) { ?>
				  <tr>
						<td class = "text-center"><img src="<?php echo $member->get_picture() ?>" alt = "photo de profil" height=100px class="rounded"> </td>
						<td class = "text-center"> <?php echo $member->get_last_name() ?> </td>
						<td class = "text-center"> <?php echo $member->get_first_name() ?> </td>
					</tr>
				  <?php } ?>
				</tbody>
			</table>
		</div>
		<?php } ?>

		<h5>URL des photos des évènements précedents :</h5>
		<div class="col-5">
			<table class="table table-bordered">
			 <thead class="thead-light">
				  <tr>
						<th class = "text-center" scope="col">Nom de l'évènement</th>
				  	<th class = "text-center" scope="col">URL</th>
				  </tr>
				</thead>
				<tbody>
				  <?php foreach ($passed_events as $indice => $event) { ?>
				  <tr>
						<td class = "text-center"> <?php echo $event->get_event_name() ?> </td>
						<td class = "text-center">
							<?php if($event->get_url_picture() != null){ ?> <a href="<?php echo $event->get_url_picture();?>"><?php echo $event->get_url_picture();?></a><?php }else{ echo "/"; } ?>
						</td>
					</tr>
				  <?php } ?>
				</tbody>
			</table>
		</div>
