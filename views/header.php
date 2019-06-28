<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" >
		<title>Projet Web</title>
		<link rel="stylesheet" href="views/bootstrap/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="views/bootstrap/css/perso.css">
	</head>
	<body class="bg-light text-dark">
	<header>
		<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
			<ul class="navbar-nav">
				<!-- <a href="index.php?action=accueil"><img src="views/images/coureur.png" alt="logo" height="50px"></a></li> -->
				<li class="nav-item active"><a class="nav-link" href="index.php?action=accueil">Accueil</a></li>
				<?php if(isset($_SESSION['authentifie'])){?>
					<li class="nav-item"><a class="nav-link" href="index.php?action=account">Profil</a></li>
					<li class="nav-item"><a class="nav-link" href="index.php?action=plan">Entrainement</a></li>
					<li class="nav-item"><a class="nav-link" href="index.php?action=event">Evenement</a></li>
					<?php if($_SESSION['specialMembre']){ ?>
						<li class="nav-item"><a class="nav-link" href="index.php?action=admin">Admin</a></li>
					<?php } ?>
					<li class="nav-item"><a class="nav-link" href="index.php?action=logout">Se déconnecter</a></li>
				<?php } ?>
			</ul>
		</nav>
	</header>
	
