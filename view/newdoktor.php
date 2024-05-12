<?php require_once __DIR__ . '/_header.php';

?>
Unos novog doktora<br>
		<form method="post" action="index.php?rt=doktor/new">
			OIB: <input type="text" name="oib" />
      <br>
			Ime: <input type="text" name="ime" />
      <br>
			Prezime: <input type="text" name="prezime" />
      <br>
      ID bolnice: <input type="text" name="id_bolnica" />
      <br>
      Plaća: <input type="text" name="placa" />
      <br>
      Područje: <input type="text" name="podrucje" />
      <br>
      Specijalizant? (1/0): <input type="text" name="specijalizant" />
      <br>
	    <button type="submit" name="gumb" value="doktor">Dodaj!</button>
		</form>

<?php require_once __DIR__ . '/_footer.php'; ?>
