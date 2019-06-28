<h3>Bienvenue sur la page de gestion de plan</h3>


<!-- SHOW THE DIFFERENTS AVAILABLE PLAN -->
<h5> Voici les différents plans d'entrainement disponible :</h5>
<p class="text-success"> <?php echo $notif_plan_delete ?> </p>
<p class="text-success"> <?php echo $notif_daily_plan_delete ?> </p>
<p class="text-success"> <?php echo $notif_daily_plan_modify ?> </p>
<p class="text-success"> <?php echo $notif_add_daily_plan ?> </p>
<form class ="col-8" action="?action=coach" method="post">
  <table class="table table-bordered">
    <thead class="thead-light">
      <tr>
        <th class = "text-center" scope="col">Nom entrainement</th>
        <th class = "text-center" scope="col">Objectif</th>
        <th class = "text-center" scope="col">Date de début</th>
        <th class = "text-center" scope="col">Date de fin</th>
        <th scope="col" class = "text-center"><input type="submit" name="seeDetails"  value="Voir les détails"></th>
        <th scope="col" class = "text-center"><input type="submit" name="delete"  value="Supprimer plan"></th>
        <th scope="col" class = "text-center"><input type="submit" name="seeParticipating"  value="Voir les participants"></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($plan_table as $indice => $plan) { ?>
          <tr>
            <td class = "text-center"> <?php echo $plan->get_plan_name() ?> </td>
            <td class = "text-center"> <?php echo $plan->get_goal() ?> </td>
            <td class = "text-center"> <?php echo $plan->get_date_start() ?> </td>
            <td class = "text-center"> <?php echo $plan->get_date_end() ?> </td>
            <td class = "text-center">
              <input type="radio" name="detailSelected" value="<?php echo $plan->get_plan_id() ?>">
            </td>
            <td class = "text-center">
              <input type="radio" name="deletePlan" <?php if($plan->get_plan_id() ==1){?>disabled ="disabled" <?php } ?> value="<?php echo $plan->get_plan_id() ?>">
            </td>
            <td class = "text-center">
              <input type="radio" name="participatingSelected" value="<?php echo $plan->get_plan_id() ?>">
            </td>
          </tr>
      <?php } ?>
    </tbody>
  </table>
</form>

<!-- SHOW THE DETAILS OF THE SELECTED PLAN -->
<?php if($show_training_details == true){ ?>
  <h5>Voici les détails du plan d'entrainement <strong><?php echo $plan_title ?></strong> : </h5>
  <form class ="col-5" action="?action=coach" method="post">
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th class = "text-center" scope="col">Date</th>
            <th class = "text-center" scope="col"><input type="submit" name="modifyDailyPlan"  value="Modifier"></th>
            <th scope="col" class = "text-center"><input type="submit" name="deleteDailyPlan"  value="Supprimer"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($plan_selected as $indice => $plan) { ?>
              <tr>
                <td class = "text-center"> <?php echo $plan->get_date() ?> </td>
                <td class = "text-center">
                  <input type="text" name="modifyDPlan[]" value="<?php echo $plan->get_description() ?>">
                </td>
                <td class = "text-center">
                  <input type="checkbox" name="deleteDPlan[]" value="<?php echo $plan->get_date() ?>">
                </td>
                <input type="hidden" name="dailyPlanId" value="<?php echo $plan->get_plan_id() ?>">
                <input type="hidden" name="dailyPlanDate[]" value="<?php echo $plan->get_date() ?>">
              </tr>
          <?php } ?>
        </tbody>
    </table>
    <br>
    <h5>Ajouter un jour d'entrainement : </h5>
    <p>
      <strong>Date</strong> : <input type="text" name="dateOfNewPlan" placeholder="JJ/MM/AAAA"><br><br>
      <strong>Description</strong> : <input type="text" name="descriptionOfNewPlan" placeholder="Entrez ici la description"><br>
      <input type="submit" name="addPlan" class="btn btn-dark">
      <input type="hidden" name="addPlanId" value="<?php echo $plan->get_plan_id() ?>">
    </p>
  </form>
<?php } ?>

  <!-- SHOW THE PARTICIPATING MEMBERS OF THE SELECTED EVENT-->
  <?php if($show_training_participating_members == true){ ?>
    <h5>Voici les participatings du plan <strong><?php echo $name_of_plan ?></strong> :</h5>
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
				  <?php foreach ($table_participating_to_plan as $indice => $member) { ?>
				  <tr>
						<td class = "text-center"><img src="<?php echo $member->get_picture() ?>" alt = "photo de profil" height=100px class="rounded"> </td>
						<td class = "text-center"> <?php echo $member->get_last_name() ?> </td>
						<td class = "text-center"> <?php echo $member->get_first_name() ?> </td>
            <td class = "text-center"> <?php echo $member->get_mail() ?> </td>
					</tr>
				  <?php } ?>
				</tbody>
			</table>
		</div>
  <?php } ?>


  <br>
  <!-- IMPORT CSV PLAN -->
  <h5>Crée un plan d'entrainement : </h5>
  <p class="text-danger"><strong><?php echo $notif_import ?></strong></p>
  <form class="col-5" enctype="multipart/form-data" action="?action=coach" method = "post">
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000"><br>
			<p><strong>Nom du plan d'entrainement :</strong> <input type="text" name="importName"></p>
			<p>Fichier .csv :<input type="file" name="plan_file"></p>
			<input class ="btn btn-dark" type="submit" name="importFile" value="Importer">
		</form>
