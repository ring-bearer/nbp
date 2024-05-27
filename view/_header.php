<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Bolnice</title>
	<link rel="stylesheet" type="text/css" href='./style/style.css'>

</head>
<body>
	<nav>
		<div class="dropdown">
  		<button class="dropbtn" onclick="window.location.href='index.php?rt=pacijent/index'">Pacijenti</button>
  		<div class="dropdown-content">
				<a href="index.php?rt=pacijent/index">Popis pacijenata</a>
  			<a href="index.php?rt=pacijent/unos">Unos novog pacijenta</a>
				<a href="index.php?rt=pacijent/promjena">Promjena podataka</a>
			</div>
		</div>
		<div class="dropdown">
  		<button class="dropbtn" onclick="window.location.href='index.php?rt=lijecnik/index'">Liječnici</button>
  		<div class="dropdown-content">
				<a href="index.php?rt=lijecnik/index">Popis liječnika</a>
  			<a href="index.php?rt=lijecnik/unos">Unos novog liječnika</a>
				<a href="index.php?rt=lijecnik/promjena">Promjena podataka</a>
			</div>
		</div>
		<div class="dropdown">
  		<button class="dropbtn" onclick="window.location.href='index.php?rt=pretraga/index'">Pretrage</button>
  		<div class="dropdown-content">
				<a href="index.php?rt=pretraga/index">Popis pretraga</a>
			</div>
		</div>
	</nav>
