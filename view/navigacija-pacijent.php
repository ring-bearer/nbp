
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
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
  		<button class="dropbtn" onclick="window.location.href='index.php?rt=pretraga/index'">Pretrage</button>
  		<div class="dropdown-content">
        <a href="index.php?rt=pretraga/povijest">Povijest pretraga</a>
				<a href="index.php?rt=pretraga/unos>">Nova pretraga</a>

			</div>
		</div>
		<button class="logout" onclick="window.location.href='index.php?rt=login/logout'">Odjavi se</button>
		<button class="logout" onclick="window.location.href='index.php?rt=profil/index'">Moj profil</button>
	</nav>
</body>
</html>
