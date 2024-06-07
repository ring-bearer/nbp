<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/bolnicaController.class.php';


echo "<h1> Promjena podataka </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
else{
	echo "<p id=gore>Ovdje možete mijenjati podatke o bolnicama.</p>";
}
?>
<form action="index.php?rt=bolnica/update" method="post">
<table id="promjena">
	<tr>
		<th>ID</th><th>Ime</th>
		<th>Adresa</th><th>Mjesto</th>
	</tr>
	<?php
    foreach($list as $a){?>
      <tr>
      <!-- Na kraju smo ipak odlucili da ne postoji brisanje bolnica zbog povezanosti sa susjednim bolnicama -->
			<td><?php echo $a->__get('id') ?></td>
			<td><input style="width: 600px" type="text" name="ime[]" value="<?php echo $a->__get('ime') ?>"></td>
			<td><input style="width: 260px" type="text" name="adresa[]" value="<?php echo $a->__get('adresa') ?>"></td>
      <td><input type="text" name="mjesto[]" value="<?php echo $a->__get('mjesto') ?>"></td>
		</tr><?php
   }
   echo '</table><br>';
   echo '<button type="submit" value="brisanje">Spremi promjene</button>';
   echo '</form>';

require_once __DIR__ . '/_footer.php'; ?>
