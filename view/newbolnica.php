<?php require_once __DIR__ . '/_header.php';

echo "<h1> Unos nove bolnice </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
?>
	<div id="unos">
		<form method="post" action="index.php?rt=bolnica/new">
			<div id="insideunos">
			Ime:<br> <input type="text" name="ime" />
      <br><br>
			Adresa:<br> <input type="text" name="adresa" />
      <br><br>
      Mjesto:<br> <input type="text" name="mjesto" />
      <br><br>
	    <button type="submit" name="gumb" value="bolnica">Dodaj!</button>
			</div>
		</form>
	</div>

<?php require_once __DIR__ . '/_footer.php'; ?>
