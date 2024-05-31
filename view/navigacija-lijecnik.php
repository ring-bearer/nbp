<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <nav>
		<div class="dropdown">
  		<button class="dropbtn" onclick="window.location.href='index.php?rt=lijecnik/mojipacijenti'">Pacijenti</button>
  		<div class="dropdown-content">
				<a href="index.php?rt=lijecnik/mojipacijenti">Popis pacijenata</a>
        <a href="index.php?rt=pacijent/unos">Unos novog pacijenta</a>
        <a href="index.php?rt=zahtjev/mojizahtjevi">Pristigli zahtjevi</a>
  			<a href="index.php?rt=zahtjev/novi">Novi zahtjev</a>
      </div>
		</div>
		<div class="dropdown">
  		<button class="dropbtn" onclick="window.location.href='index.php?rt=pretraga/index'">Pretrage</button>
  		<div class="dropdown-content">
				<a href="index.php?rt=pretraga/index">Popis pretraga</a>
			</div>
		</div>
		<button class="logout" onclick="window.location.href='index.php?rt=login/logout'">Odjavi se</button>
		<button class="logout" onclick="window.location.href='index.php?rt=profil/index'">Moj profil</button>
	</nav>
</body>

</html>
