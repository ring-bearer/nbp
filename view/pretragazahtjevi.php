<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pretragaController.class.php';

echo '<h1> Zahtjevi za novom pretragom </h1>';
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
if(!isset($prazno)){
?>
<form action="index.php?rt=pacijent/pretraga" method="post">
<table>
	<tr>
		<th>Zahtjev postavlja:</th><th>OIB pacijenta</th>
    <?php if ($_COOKIE['ovlasti']==='2'){
    echo '<th>Zahtjev zaprima:</th><th>OIB liječnika</th>';
    }?>
		<th>Vrsta pretrage</th>
		<?php if ($_COOKIE['ovlasti']==='0'){
    echo '<th></th><th></th>';
    }?>
	</tr>
	<?php
    $i=0;
    foreach($list as $a){
			echo '<tr>';
      echo '<td>' . $listapac[$i]->__get('prezime') . ', ' . $listapac[$i]->__get('ime') . '</td>';
      echo '<td>' . $a[0]. '</td>';
      if ($_COOKIE['ovlasti']==='2'){
        echo '<td>' . $listalijec[$i]->__get('prezime') . ', ' . $listalijec[$i]->__get('ime') . '</td>';
        echo '<td>' . $a[1] . '</td>';
      }
      echo '<td>' . $a[2] . '</td>';
      if ($_COOKIE['ovlasti']==='0'){?>
        <!-- Za slanje oiba pacijenta i oiba lijecnika-->
        <input type="hidden" name="oib_pacijenta_<?php echo $i; ?>" value="<?php echo $a[0]; ?>">
            <input type="hidden" name="oib_lijecnika_<?php echo $i; ?>" value="<?php echo $a[1]; ?>">
            <input type="hidden" name="vrsta_pretrage_<?php echo $i; ?>" value="<?php echo $a[2]; ?>">
            <input type="hidden" name="mjesto_<?php echo $i; ?>" value="<?php echo $listapac[$i]->__get('mjesto'); ?>">
            <td><button type="submit" name="prihvati_<?php echo $i; ?>" value="<?php echo $listapac[$i]->__get('oib'); ?>">Prihvati</button></td>
            <td><button type="submit" name="odbij_<?php echo $i; ?>" value="<?php echo $listapac[$i]->__get('oib'); ?>">Odbij</button></td>
      <?php }
      // Potrebno za pravilno slanje imena
      $i++;
      echo '<tr>';
   }?>
</table>
</form>

<?php }
require_once __DIR__ . '/_footer.php'; ?>
