<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pacijentController.class.php';

if(isset($_COOKIE['ovlasti']) && $_COOKIE['ovlasti'] === '0'){
	require_once __DIR__ . '/navigacija-lijecnik.php';
}
else if(isset($_COOKIE['ovlasti']) && $_COOKIE['ovlasti'] === '1'){
	require_once __DIR__ . '/navigacija-pacijent.php';
}
else{
	require_once __DIR__ . '/navigacija-admin.php';
}

if(isset($poruka)) echo $poruka;
else echo "Odaberite pacijente koje želite ukloniti iz baze.";
?>
<form action="index.php?rt=pacijent/delete" method="post">
<table>
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
				echo '<td>' . $a->__get('oib') . '</td>';
				echo '<td>' . $a->__get('mbo') . '</td>';
        echo '<td>' . $a->__get('ime') . '</td>';
				echo '<td>' . $a->__get('prezime') . '</td>';
				echo '<td>' . $a->__get('datum_rodjenja') . '</td>';
				echo '<td>' . $a->__get('adresa') . '</td>';
				echo '<td>' . $a->__get('mjesto') . '</td>';
        echo '<td>' . $a->__get('oib_lijecnika') . '</td>';
        echo '</tr>';
    }
	echo '</table><br>';
  echo '<button type="submit" value="brisanje">Spremi promjene</button>';
  echo '</form>';
require_once __DIR__ . '/_footer.php';
?>
