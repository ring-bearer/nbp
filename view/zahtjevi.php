<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pretragaController.class.php';

echo '<h1> Zahtjevi za prebacivanje pacijenata </h1>';
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
if(!isset($prazno)){

?>
<table>
	<tr>
		<th>Zahtjev postavlja:</th><th>Zahtjev zaprima:</th><th>OIB pacijenta</th>
    <th>MBO</th><th>Ime</th><th>Prezime</th>
		<th>Datum rođenja</th><th>Adresa</th><th>Mjesto</th>
    <th></th>
	</tr>
	<?php
    $i=0;
    foreach($listapac as $a){
			echo '<tr>';
			echo '<td>' . $listastarih[$i]->__get('prezime') . ', ' . $listastarih[$i]->__get('ime') . '</td>';
      echo '<td>' . $listanovih[$i]->__get('prezime') . ', ' . $listanovih[$i]->__get('ime') . '</td>';?>
      <?php
      echo '<td>' . $a->__get('oib') . '</td>';
      echo '<td>' . $a->__get('mbo') . '</td>';
      echo '<td>' . $a->__get('ime') . '</td>';
      echo '<td>' . $a->__get('prezime') . '</td>';
      echo '<td>' . $a->__get('datum_rodjenja') . '</td>';
      echo '<td>' . $a->__get('adresa') . '</td>';
      echo '<td>' . $a->__get('mjesto') . '</td>';?>

			<form action="index.php?rt=pacijent/transfer" method="post">
			<td><input type="hidden" name="oib_novi" value="<?php echo $listanovih[$i]->__get('oib');?>">
      <button type="submit" name="oib_pacijenta" value="<?php echo $a->__get('oib');?>">Prihvati</button></td>
			</form>
		</tr><?php
    $i++;
   }?>
</table>

<?php }
require_once __DIR__ . '/_footer.php'; ?>
