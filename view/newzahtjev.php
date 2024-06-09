<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pacijentController.class.php';
echo "<h1> Zahtjev za prebacivanjem pacijenta </h1>";
if(isset($poruka)) echo "<p id=gore>" . $poruka . "</p>";
else{
	echo "<p id=gore>Odaberite drugog liječnika iz Vaše ambulante za željenog pacijenta.</p>";
}
if(!isset($prazno)){
?>
<table>
	<tr>
    <th>
    <th>OIB</th><th>MBO</th><th>Ime</th><th>Prezime</th>
		<th>Datum rođenja</th><th>Adresa</th><th>Mjesto</th>
  </tr>

  <form action="index.php?rt=zahtjev/new" method="post">
	<?php
    foreach ($list as $a){
        echo '<tr><td><select name="novi">';
          foreach($lijeclist as $b){
            if($b->__get('oib')===$_COOKIE['oib']) continue;
            ?><option value="<?php echo $b->__get('oib')?>"><?php
              echo $b->__get('prezime') . ', ' . $b->__get('ime') . '</option>';
            }
        echo '</select></td>';
				echo '<td>' . $a->__get('oib') . '</td>';
				echo '<td>' . $a->__get('mbo') . '</td>';
        echo '<td>' . $a->__get('ime') . '</td>';
				echo '<td>' . $a->__get('prezime') . '</td>';
				echo '<td>' . $a->__get('datum_rodjenja') . '</td>';
				echo '<td>' . $a->__get('adresa') . '</td>';
				echo '<td>' . $a->__get('mjesto') . '</td>';?>
        <td><button type="submit" name="pacijent" value="<?php echo $a->__get('oib')?>">Spremi</button></td>
      </tr><?php
    }
	?>
  </form>
  </table>
  <br>
<?php
}
 require_once __DIR__ . '/_footer.php'; ?>
