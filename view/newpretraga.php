<?php require_once __DIR__ . '/_header.php';

echo "<h1> Zahtjev za novom pretragom </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
else echo "<p id=gore>Pošaljite svom liječniku opće prakse zahtjev za uputnicom.</p>"
?>
	<div id="unos">
		<form method="post" action="index.php?rt=pretraga/new">
			<div id="insideunos">
			  <select name="zahtjev">
			    <!--<option value="0">Odaberite pregled:</option> -->
			    <option value="dermatološki pregled">Dermatološki pregled</option>
			    <option value="oftalmološki pregled">Oftalmološki pregled</option>
			    <option value="fizikalna medicina">Fizikalna medicina</option>
			    <option value="magnetska rezonanca">Magnetska rezonanca</option>
			    <option value="serologija">Serologija</option>
			    <option value="dijabetologija">Dijabetologija</option>
			  </select>
	    <button type="submit" value="vrsta">Pošalji!</button>
			</div>
		</form>
	</div>

<?php require_once __DIR__ . '/_footer.php'; ?>
