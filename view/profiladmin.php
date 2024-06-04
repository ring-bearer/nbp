<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/lijecnikController.class.php';


echo "<h1> Promjena podataka </h1>";

?>
<form action="index.php?rt=profil/update" method="post">
<table id="promjena">
	<tr>
		<th>OIB</th><th>Ime</th>
		<th>Prezime</th>
	</tr>
	
	<tr>
	<td><?php echo $user->__get('oib') ?></td>
	<td><input type="text" name="ime[]" value="<?php echo $user->__get('ime') ?>"></td>
	<td><input type="text" name="prezime[]" value="<?php echo $user->__get('prezime') ?>"></td>
	</tr>
	<?php
   echo '</table><br>';
   echo '<button type="submit" value="brisanje">Spremi promjene</button>';
   echo '</form>';

require_once __DIR__ . '/_footer.php'; ?>
