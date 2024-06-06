<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pretragaController.class.php';

echo '<h1> Zahtjevi za novom pretragom </h1>';
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
if(!isset($prazno)){
?>
<form action="index.php?rt=pacijent/transfer" method="post">
<table>
	<tr>
		<th>Zahtjev postavlja:</th><th>OIB pacijenta</th>
    <?php if ($_COOKIE['ovlasti']==='2'){
    echo '<th>Zahtjev zaprima:</th><th>OIB liječnika</th>';
    }?>
		<th>Vrsta pretrage</th>
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
        <td><button type="submit" name="prihvati" value="<?php $listapac[$i]->__get('oib');?>">Prihvati</button></td>
        <td><button type="submit" name="odbij" value="<?php $listapac[$i]->__get('oib');?>">Odbij</button></td>
      <?php }
      echo '<tr>';
   }?>
</table>
</form>

<?php }
require_once __DIR__ . '/_footer.php'; ?>
