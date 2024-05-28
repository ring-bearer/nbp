<?php require_once __DIR__ . '/_header.php';

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
			Datum rođenja: <br><input type="date" name="datum_rodjenja" />
      <br>
			Adresa:<br> <input type="text" name="adresa" />
      <br>
			Mjesto: <br><input type="text" name="mjesto" />
      <br>
			OIB liječnika:<br> <input type="text" name="oib_lijecnika" />
      <br>
	    <button type="submit" name="gumb" value="pacijent">Dodaj!</button>
		</div>
		</form>
	</div>
<?php require_once __DIR__ . '/_footer.php'; ?>
