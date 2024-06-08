<?php require_once __DIR__ . '/_header.php';
?>

<h1> Prijedlozi termina </h1>
<?php
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
if(!isset($prazno)){
    $i=0;
    $j=0;
  foreach($list as $b){
    if($b!=false){
    echo '<p id="gore">' . ucfirst($pretrage[$i]->__get('vrsta')) . ':</p>';
    echo '<table>
    	<tr>
    		<th>Datum</th><th>Vrijeme</th>
    		<th>Bolnica</th><th></th>
    	</tr>';
    foreach($b as $a){
			echo '<tr>';
			echo '<td>' . $a->__get('datum') . '</td>';
			echo '<td>' . $a->__get('vrijeme') . '</td>';
			echo '<td>' . $bolnice[$j]->__get('ime') . ', ' . $bolnice[$j]->__get('mjesto') . '</td>';?>
      <td>
        <form action="index.php?rt=pacijent/termin" method="post">
        <input type="hidden" name="datum" value="<?php echo $a->__get('datum') ?>">
        <input type="hidden" name="vrijeme" value="<?php echo $a->__get('vrijeme') ?>">
      <input type="hidden" name="id_bolnice" value="<?php echo $a->__get('id_bolnice') ?>">
      <input type="hidden" name="id_pretrage" value="<?php echo $pretrage[$i]->__get('id'); ?>">
      <button type="submit" name="oib" value="<?php echo $_COOKIE['oib'];?>">Prihvati</button>
      </form>
      </td>
      <?php
      echo '</tr>';
      $j++;
   }
   echo '</table><br>';
 }
 $i++;
 }
}
   ?>

<?php require_once __DIR__ . '/_footer.php'; ?>
