<?php require_once __DIR__ . '/_header.php';
if(isset($poruka)) echo $poruka;
else echo "Unos novog liječnika:";
?>
		<br>
		<form method="post" action="index.php?rt=lijecnik/new">
			OIB: <input type="text" name="oib" />
      <br>
			Ime: <input type="text" name="ime" />
      <br>
			Prezime: <input type="text" name="prezime" />
      <br>
      Datum rođenja: <input type="date" name="datum_rodjenja" />
      <br>
      Adresa ambulante: <input type="text" name="adresa_ambulante" />
      <br>
      Mjesto ambulante: <input type="text" name="mjesto_ambulante" />
      <br>
	    <button type="submit" name="gumb" value="doktor">Dodaj!</button>
		</form>

<?php require_once __DIR__ . '/_footer.php'; ?>
