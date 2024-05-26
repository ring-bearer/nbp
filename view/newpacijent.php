<?php require_once __DIR__ . '/_header.php';


if(isset($poruka)) echo $poruka;
else echo "Unos novog pacijenta:";
?>
		<br>
		<form method="post" action="index.php?rt=pacijent/new">
			OIB: <input type="text" name="oib" />
      <br>
			MBO: <input type="text" name="mbo" />
      <br>
			Ime: <input type="text" name="ime" />
      <br>
			Prezime: <input type="text" name="prezime" />
      <br>
			Datum rođenja: <input type="date" name="datum_rodjenja" />
      <br>
			Adresa: <input type="text" name="adresa" />
      <br>
			Mjesto: <input type="text" name="mjesto" />
      <br>
			OIB liječnika: <input type="text" name="oib_lijecnika" />
      <br>
	    <button type="submit" name="gumb" value="pacijent">Dodaj!</button>
		</form>

<?php require_once __DIR__ . '/_footer.php'; ?>
