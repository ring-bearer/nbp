<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pacijentController.class.php';
?>

<h1>Moji pacijenti</h1>
<table>
	<tr>
    <th>OIB</th><th>MBO</th><th>Ime</th><th>Prezime</th>
		<th>Datum rođenja</th><th>Adresa</th><th>Mjesto</th>
  </tr>
	<?php
    foreach ($list as $a){
        echo '<tr>';
				echo '<td>' . $a->__get('oib') . '</td>';
				echo '<td>' . $a->__get('mbo') . '</td>';
        echo '<td>' . $a->__get('ime') . '</td>';
				echo '<td>' . $a->__get('prezime') . '</td>';
				echo '<td>' . $a->__get('datum_rodjenja') . '</td>';
				echo '<td>' . $a->__get('adresa') . '</td>';
				echo '<td>' . $a->__get('mjesto') . '</td>';
        echo '</tr>';
    }
	?>
  </table>
<?php require_once __DIR__ . '/_footer.php'; ?>
