<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/lijecnikController.class.php';


echo "<h1> Promjena podataka </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
else{
	echo "<p id=gore>Tu možete promijeniti neke od svojih podataka.</p>";
}
?>
<form action="index.php?rt=profil/updatePacijent" method="post">
<table id="promjena">
	<tr>
		<th>OIB</th><th>MBO</th><th>Ime</th>
		<th>Prezime</th><th>Datum rođenja</th>
		<th>Adresa</th><th>Mjesto</th>
		<th>Liječnik</th>
	</tr>

	<tr>
	<td><?php echo $user->__get('oib') ?></td>
    <td><?php echo $user->__get('mbo') ?></td>
	<td><input type="text" name="ime" value="<?php echo $user->__get('ime') ?>"></td>
	<td><input type="text" name="prezime" value="<?php echo $user->__get('prezime') ?>"></td>
	<td><input type="date" name="datum_rodjenja" value="<?php echo $user->__get('datum_rodjenja') ?>"></td>
	<td><input type="text" name="adresa" value="<?php echo $user->__get('adresa') ?>"></td>
	<td><input type="text" name="mjesto" value="<?php echo $user->__get('mjesto') ?>"></td>
	<td><?php echo $doktor->__get('prezime') . ', ' . $doktor->__get('ime') ?></td>
	</tr>
	<?php
   echo '</table><br>';
   echo '<button type="submit" value="brisanje">Spremi promjene</button>';
   echo '</form>';

require_once __DIR__ . '/_footer.php'; ?>
