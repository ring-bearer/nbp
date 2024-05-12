<?php require_once __DIR__ . '/_header.php';

?>
Unos novog pacijenta<br>
		<form method="post" action="index.php?rt=pacijent/new">
			OIB: <input type="text" name="oib" />
      <br>
			MBO: <input type="text" name="mbo" />
      <br>
			Ime: <input type="text" name="ime" />
      <br>
			Prezime: <input type="text" name="prezime" />
      <br>
	    <button type="submit" name="gumb" value="pacijent">Dodaj!</button>
		</form>

<?php require_once __DIR__ . '/_footer.php'; ?>
