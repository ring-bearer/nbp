<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pretragaController.class.php';

echo '<h1> Zahtjevi za prebacivanje pacijenata </h1>';
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
if(!isset($prazno)){
?>
<form action="index.php?rt=pacijent/transfer" method="post">
<table>
	<tr>
		<th>Zahtjev postavlja:</th><th>Zatjev zaprima:</th><th>OIB pacijenta</th>
    <th>MBO</th><th>Ime</th><th>Prezime</th>
		<th>Datum rođenja</th><th>Adresa</th><th>Mjesto</th>
    <th></th>
	</tr>
	<?php
    $i=0;
    foreach($listapac as $a){
			echo '<tr>';
			echo '<td>' . $listastarih[$i]->__get('prezime') . ', ' . $listastarih[$i]->__get('ime') . '</td>';
      echo '<td>' . $listanovih[$i]->__get('prezime') . ', ' . $listanovih[$i]->__get('ime') . '</td>';
      echo '<td>' . $a->__get('oib') . '</td>';
      echo '<td>' . $a->__get('mbo') . '</td>';
      echo '<td>' . $a->__get('ime') . '</td>';
      echo '<td>' . $a->__get('prezime') . '</td>';
      echo '<td>' . $a->__get('datum_rodjenja') . '</td>';
      echo '<td>' . $a->__get('adresa') . '</td>';
      echo '<td>' . $a->__get('mjesto') . '</td>';?>
      <td><button type="submit" name="transfer" value="<?php echo $a->__get('oib');?>">Prihvati</button></td>
    </tr><?php
    $i++;
   }?>
</table>
</form>

<?php }
require_once __DIR__ . '/_footer.php'; ?>
