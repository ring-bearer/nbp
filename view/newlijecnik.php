<?php require_once __DIR__ . '/_header.php';

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
			Najbliža bolnica:<br>
			<?php
	        echo '<tr><td><select name="bolnica">';
						echo '<option value="" selected disabled hidden>Odaberite bolnicu</option>';
	          foreach($list as $b){
	            ?><option value="<?php echo $b->__get('id')?>"><?php echo $b->__get('ime')?><?php
	            }
	        echo '</select></td>';
					?>
      <br><br>
	    <button type="submit" name="gumb" value="doktor">Dodaj!</button>
			</div>
		</form>
	</div>

<?php require_once __DIR__ . '/_footer.php'; ?>
