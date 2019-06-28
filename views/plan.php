	<h4>Bievenue sur la page des plans d'entrainement </h4>
	<h6> Vous suivez le plan "<strong> <?php echo htmlspecialchars($actual_member_following_plan->get_plan_name()) ?> </strong>"</h6>
	<br>
	<!--Show the button to see the daily/weekly/complete training -->
	<form class="form-check" action="?action=plan" method="post">
		<input class="btn btn-dark" type="submit" name = "daily" value="Entrainement du jour"/>
		<input class="btn btn-dark" type="submit" name = "weekly" value="Entrainement de la semaine"/>
		<input class="btn btn-dark" type="submit" name = "complete" value="Entrainement complet"/>
	</form>
	<br>

	<?php if($show_daily_training == true){ ?>
		<?php if(isset($plan_of_the_day)){ ?>
			<p><strong> Voici le plan d'aujourd'hui : </strong><span class="text-success">- <?php echo $plan_of_the_day ?></span></p>
		<?php }else{ ?>
			<p><strong> Vous n'avez pas d'entrainement prévu pour aujourd'hui </strong></p>
		<?php } ?>
	<?php }elseif($show_weekly_training == true){ ?>
		<h5>Voici votre plan d'entrainement de la semaine :  </h5>
		<div class="col-5">
			<table class="table table-bordered">
				<thead class="thead-light">
					<tr>
						<th class = "text-center" scope="col">Date</th>
						<th class = "text-center" scope="col">Description</th>
					</tr>
				</thead>
					<?php foreach ($plan_of_member_week as $indice => $plan) { ?>
						<tr>
							<td class = "text-center"> <?php echo $plan->get_date() ?> </td>
							<td class = "text-center"> <?php echo $plan->get_description() ?> </td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<br><br>
	<?php }else{ ?>
		<h5>Voici votre plan d'entrainement au complet :  </h5>
		<div class="col-5">
			<table class="table table-bordered">
				<thead class="thead-light">
					<tr>
						<th class = "text-center" scope="col">Date</th>
						<th class = "text-center" scope="col">Description</th>
					</tr>
				</thead>
					<?php foreach ($plan_of_member as $indice => $plan) { ?>
						<tr>
							<td class = "text-center"> <?php echo $plan->get_date() ?> </td>
							<td class = "text-center"> <?php echo $plan->get_description() ?> </td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<br><br>
	<?php } ?>

	<form action = "?action=plan" method="POST">
		<p>Exportez votre plan : <input class="btn btn-dark" type = "submit" name="export" value="Exporter plan"></p>
	</form>

	<!--SHOW THE TABLE WITH THE DIFFERENTS AVAILABLE PLANS-->
	<h5> Voici les différents plans d'entrainement disponible :</h5>
	<p class="text-success"><strong> <?php echo $notif_training_change ?> </strong> </p>
	<form class ="col-8" action="?action=plan" method="post">
		<table class="table table-bordered">
		  <thead class="thead-light">
		    <tr>
					<th class = "text-center" scope="col">Nom entrainement</th>
					<th class = "text-center" scope="col">Objectif</th>
					<th class = "text-center" scope="col">Date de début</th>
					<th class = "text-center" scope="col">Date de fin</th>
					<th scope="col" class = "text-center"><input type="submit" name="choosedPlan" value="Choisir"></th>
					<th scope="col" class = "text-center"><input type="submit" name="seeDetails"  value="Voir les détails"></th>
		    </tr>
		  </thead>
		  <tbody>
		    <?php foreach ($following_plan_table as $indice => $plan) { ?>
			    	<tr>
							<td class = "text-center"> <?php echo $plan->get_plan_name() ?> </td>
							<td class = "text-center"> <?php echo $plan->get_goal() ?> </td>
							<td class = "text-center"> <?php echo $plan->get_date_start() ?> </td>
							<td class = "text-center"> <?php echo $plan->get_date_end() ?> </td>
							<td class = "text-center">
								<input type="radio" name="planSelected" <?php if($actual_member_following_plan->get_plan_id() == $plan->get_plan_id()){
									?> checked="checked"
								<?php } ?>
								value="<?php echo $plan->get_plan_id() ?>">
							</td>
							<td class = "text-center">
								<input type="radio" name="detailSelected" value="<?php echo $plan->get_plan_id() ?>">
							</td>
						</tr>
		    <?php } ?>
		  </tbody>
		</table>
	</form>

	<?php if($show_training_details == true){ ?>
		<h5>Voici les détails du plan d'entrainement <strong><?php echo $plan_title ?></strong> : </h5>
		<div class="col-5">
			<table class="table table-bordered">
				<thead class="thead-light">
					<tr>
						<th class = "text-center" scope="col">Date</th>
						<th class = "text-center" scope="col">Description</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($plan_selected as $indice => $plan) { ?>
							<tr>
								<td class = "text-center"> <?php echo $plan->get_date() ?> </td>
								<td class = "text-center"> <?php echo $plan->get_description() ?> </td>
							</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
  <?php } ?>


	<!-- Show the button if it's a coach-->
	<?php if($_SESSION['coach']){ ?>
		<form class="form-check">
			<br><input class="btn btn-dark" type="button" value="Gestion des plans d'entrainement" onclick="window.location.href= 'index.php?action=coach'"/>
		</form>
	<?php } ?>
