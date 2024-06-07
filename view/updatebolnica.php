<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/bolnicaController.class.php';


echo "<h1> Promjena podataka </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
else{
	echo "<p id=gore>Ako želite ukloniti bolnicu iz baze, označite kućicu lijevo od njega.</p>";
}
?>
<form action="index.php?rt=bolnica/update" method="post">
<table id="promjena">
	<tr>
		<th>Brisanje:</th><th>ID</th><th>Ime</th>
		<th>Adresa</th><th>Mjesto</th>
	</tr>
	<?php
    foreach($list as $a){?>
      <tr>
	  		<td><input type="checkbox" name="brisanje[]" value="<?php echo $a->__get('id')?>"></td>
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
