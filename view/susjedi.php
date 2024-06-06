<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/bolnicaController.class.php';
?>

<h1>Susjedi bolnica</h1>
<?php
  echo '<p id="gore">Susjedi od ' . $l->__get('ime') . ', ' . $l->__get('mjesto') . '</p>';
  echo '<table>';
  echo '<tr><th>Ime</th><th>Adresa</th><th>Mjesto</th></tr>';
    foreach($sus as $c){
      echo '<tr>';
      echo '<td>' . $c->__get('ime') . '</td>';
      echo '<td>' . $c->__get('adresa') . '</td>';
      echo '<td>' . $c->__get('mjesto') . '</td>';
      echo '</tr>';
   }
   echo '</table>';
?>

<?php require_once __DIR__ . '/_footer.php'; ?>
