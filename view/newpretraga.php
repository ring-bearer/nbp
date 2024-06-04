<?php require_once __DIR__ . '/_header.php';

echo "<h1> Zahtjev za novom pretragom </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
else echo "<p id=gore>Pošaljite svom liječniku opće prakse zahtjev za uputnicom.</p>"
?>
	<div id="unos">
		<form method="post" action="index.php?rt=pretraga/new">
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
