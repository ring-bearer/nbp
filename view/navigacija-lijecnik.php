<?php require_once __DIR__ . '/_header.php'; ?>

    <nav>
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
		<button class="logout" onclick="window.location.href='index.php?rt=login/logout'">Odjavi se</button>
	</nav>
</body>

</html>