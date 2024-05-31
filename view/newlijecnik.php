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

echo "<h1> Unos novog liječnika </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
?>
	<div id="unos">
		<form method="post" action="index.php?rt=lijecnik/new">
			<div id="insideunos">
			OIB:<br><input type="text" name="oib" />
      <br><br>
			Ime:<br> <input type="text" name="ime" />
      <br><br>
			Prezime:<br> <input type="text" name="prezime" />
      <br><br>
      Datum rođenja:<br> <input type="date" name="datum_rodjenja" />
      <br><br>
      Adresa ambulante:<br> <input type="text" name="adresa_ambulante" />
      <br><br>
      Mjesto ambulante:<br> <input type="text" name="mjesto_ambulante" />
      <br><br>
	    <button type="submit" name="gumb" value="doktor">Dodaj!</button>
			</div>
		</form>
	</div>

<?php require_once __DIR__ . '/_footer.php'; ?>
