<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pacijentController.class.php';

echo "<h1> Promjena podataka </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
else{
	echo "<p id=gore>Ako želite ukloniti pacijenta iz baze, označite kućicu lijevo od njega.</p>";
}
?>

<form action="index.php?rt=pacijent/update" method="post">
<table id="promjena">
	<tr>
    <th>Brisanje:</th><th>OIB</th><th>MBO</th><th>Ime</th><th>Prezime</th>
		<th>Datum rođenja</th><th>Adresa</th><th>Mjesto</th>
    <th>OIB liječnika</th>
  </tr>
	<?php
    foreach ($list as $a){?>
        <tr>
        <td><input type="checkbox" name="brisanje[]" value="<?php echo $a->__get('oib')?>"></td>
        <?php
				echo '<td>' . $a->__get('oib') . '</td>';?>
				<td><input type="text" name="mbo[]" value="<?php echo $a->__get('mbo') ?>"></td>
				<td><input type="text" name="ime[]" value="<?php echo $a->__get('ime') ?>"></td>
				<td><input type="text" name="prezime[]" value="<?php echo $a->__get('prezime') ?>"></td>
				<td><input type="date" name="datum_rodjenja[]" value="<?php echo $a->__get('datum_rodjenja') ?>"></td>
				<td><input type="text" name="adresa[]" value="<?php echo $a->__get('adresa') ?>"></td>
				<td><input type="text" name="mjesto[]" value="<?php echo $a->__get('mjesto') ?>"></td>
	      <td><input type="text" name="oib_lijecnika[]" value="<?php echo $a->__get('oib_lijecnika') ?>"></td>
				</tr><?php
    }
	echo '</table><br>';
  echo '<button type="submit" value="brisanje">Spremi promjene</button>';
  echo '</form>';
require_once __DIR__ . '/_footer.php';
?>
