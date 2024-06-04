<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/lijecnikController.class.php';


echo "<h1> Promjena podataka </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
else{
	echo "<p id=gore>Tu možete promijeniti neke od svojih podataka.</p>";
}
?>
<form action="index.php?rt=profil/updateLijecnik" method="post">
<table id="promjena">
	<tr>
		<th>OIB</th><th>Ime</th>
		<th>Prezime</th><th>Datum rođenja</th>
		<th>Adresa ambulante</th><th>Mjesto ambulante</th>
	</tr>
	
	<tr>
	<td><?php echo $user->__get('oib') ?></td>
	<td><input type="text" name="ime" value="<?php echo $user->__get('ime') ?>"></td>
	<td><input type="text" name="prezime" value="<?php echo $user->__get('prezime') ?>"></td>
	<td><input type="date" name="datum_rodjenja" value="<?php echo $user->__get('datum_rodjenja') ?>"></td>
	<td><?php echo $user->__get('adresa_ambulante') ?></td>
	<td><?php echo $user->__get('mjesto_ambulante') ?></td>
	</tr>
	<?php
   echo '</table><br>';
   echo '<button type="submit" value="brisanje">Spremi promjene</button>';
   echo '</form>';

require_once __DIR__ . '/_footer.php'; ?>
