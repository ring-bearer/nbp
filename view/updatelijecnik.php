<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/lijecnikController.class.php';

if(isset($_COOKIE['ovlasti']) && $_COOKIE['ovlasti'] === '0'){
	require_once __DIR__ . '/navigacija-lijecnik.php';
}
else if(isset($_COOKIE['ovlasti']) && $_COOKIE['ovlasti'] === '1'){
	require_once __DIR__ . '/navigacija-pacijent.php';
}
else{
	require_once __DIR__ . '/navigacija-admin.php';
}

echo "<h1> Promjena podataka </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
else{
	echo "<p id=gore>Ako želite ukloniti liječnika iz baze, označite kućicu lijevo od njega.</p>";
}
?>
<form action="index.php?rt=lijecnik/update" method="post">
<table id="promjena">
	<tr>
		<th>Brisanje:</th><th>OIB</th><th>Ime</th>
		<th>Prezime</th><th>Datum rođenja</th>
		<th>Adresa ambulante</th><th>Mjesto ambulante</th>
	</tr>
	<?php
    foreach($list as $a){?>
      <tr>
      <td><input type="checkbox" name="brisanje[]" value="<?php echo $a->__get('oib')?>"></td>
			<td><?php echo $a->__get('oib') ?></td>
			<td><input type="text" name="ime[]" value="<?php echo $a->__get('ime') ?>"></td>
			<td><input type="text" name="prezime[]" value="<?php echo $a->__get('prezime') ?>"></td>
			<td><input type="date" name="datum_rodjenja[]" value="<?php echo $a->__get('datum_rodjenja') ?>"></td>
			<td><input type="text" name="adresa_ambulante[]" value="<?php echo $a->__get('adresa_ambulante') ?>"></td>
      <td><input type="text" name="mjesto_ambulante[]" value="<?php echo $a->__get('mjesto_ambulante') ?>"></td>
		</tr><?php
   }
   echo '</table><br>';
   echo '<button type="submit" value="brisanje">Spremi promjene</button>';
   echo '</form>';

require_once __DIR__ . '/_footer.php'; ?>