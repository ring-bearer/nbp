<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pretragaController.class.php';

?>
<h1> Zahtjevi za prebacivanje pacijenta </h1>

<table>
	<tr>
		<th>Zahtjev postavlja:</th><th>OIB pacijenta</th>
    <th>MBO</th><th>Ime</th><th>Prezime</th>
		<th>Datum rođenja</th><th>Adresa</th><th>Mjesto</th>
    <th></th>
	</tr>
	<?php
    $i=0;
    foreach($listapac as $a){
			echo '<tr>';
			echo '<td>' . $listalijec[$i]->__get('prezime') . ', ' . $listalijec[$i]->__get('ime') . '</td>';
			echo '<td>' . $a->__get('oib') . '</td>';
      echo '<td>' . $a->__get('mbo') . '</td>';
      echo '<td>' . $a->__get('ime') . '</td>';
      echo '<td>' . $a->__get('prezime') . '</td>';
      echo '<td>' . $a->__get('datum_rodjenja') . '</td>';
      echo '<td>' . $a->__get('adresa') . '</td>';
      echo '<td>' . $a->__get('mjesto') . '</td>';?>
      <td><button type="submit" value="<?php echo $a->__get('oib');?>">Prihvati</button></td>
    </tr><?php
    $i++;
   }?>
</table>

<?php require_once __DIR__ . '/_footer.php'; ?>
