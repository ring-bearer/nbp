<?php require_once __DIR__ . '/_header.php';

if(isset($_COOKIE['ovlasti']) && $_COOKIE['ovlasti'] === '0'){
	require_once __DIR__ . '/navigacija-lijecnik.php';
}
else if(isset($_COOKIE['ovlasti']) && $_COOKIE['ovlasti'] === '1'){
	require_once __DIR__ . '/navigacija-pacijent.php';
}
else{
	require_once __DIR__ . '/navigacija-admin.php';
}

echo "<h1> Unos novog pacijenta </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
?>
	<div id="unos">
		<form method="post" action="index.php?rt=pacijent/new">
			<div id="insideunos">
			OIB:<br> <input type="text" name="oib" />
      <br>
			MBO:<br> <input type="text" name="mbo" />
      <br>
			Ime: <br><input type="text" name="ime" />
      <br>
			Prezime: <br><input type="text" name="prezime" />
      <br>
			<?php
			if(isset($_COOKIE['ovlasti']) && $_COOKIE['ovlasti'] === '0'){?>
				Lozinka:<br><input type="password" name="pass1"/>
				<br>
				Potvrdite lozinku:<br><input type="password" name="pass2"/>
				<br>
				<?php
			}
			?>
			Datum rođenja: <br><input type="date" name="datum_rodjenja" />
      <br>
			Adresa:<br> <input type="text" name="adresa" />
      <br>
			Mjesto: <br><input type="text" name="mjesto" />
      <br>
			<?php
			if(isset($_COOKIE['ovlasti']) && $_COOKIE['ovlasti'] !== '0'){?>
				OIB liječnika:<br><input type="text" name="oib_lijecnika"/>
				<br><?php
			}
			?>

	    <button type="submit" name="gumb" value="pacijent">Dodaj!</button>
		</div>
		</form>
	</div>
<?php require_once __DIR__ . '/_footer.php'; ?>
