<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/lijecnik.class.php';
require_once __DIR__ . '/bolnica.class.php';
require_once __DIR__ . '/termin.class.php';

class TerminService{

  function getprijedlozitermin($oib,$id_pretrage){
    try
    {
      $db = DB::getConnection();
      $st = $db->prepare('SELECT oib_pacijenta, id_pretrage, datum, vrijeme, id_bolnice FROM nbp_prijedlozi_termin
          where oib_pacijenta=:oib and id_pretrage=:id_pretrage');
      $st->execute(array( 'oib'=>$oib, 'id_pretrage'=>$id_pretrage));
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr=array();
    while(1){
      $row = $st->fetch();
  		if( $row === false )
  			return $arr;
  		else{
        $i=new Termin($row['oib_pacijenta'],
  					$row['id_pretrage'],$row['datum'],
						$row['vrijeme'],$row['id_bolnice']);
        $arr[]=$i;

		  }
    }
    return $arr;
  }

  function newtermin($novi){
    try
  		{
  			$db = DB::getConnection();

  			$st = $db->prepare('INSERT INTO nbp_termin values (:a,:b,:c,:d,:e)');
        $st->execute(array( 'a' => $novi->__get('oib_pacijenta'), 'b' => $novi->__get('id_pretrage'),
  				'c' => $novi->__get('datum'), 'd' => $novi->__get('vrijeme'),
  				'e' => $novi->__get('id_bolnice')));
  		}
  		catch( PDOException $e ) {
        if($st->errorCode()==='23505'){
          $poruka="Već imate zakazanu pretragu u ovom terminu!";
          return $poruka;
        }
        exit( 'PDO error ' . $e->getMessage() ); }

  		return "Termin uspješno zakazan! Možete ga vidjeti na popisu naručenih pretraga.\n";
  }

  function deleteprijedlozitermin($oib,$id_pretrage){
    try
    {
      $db = DB::getConnection();
      $st = $db->prepare('DELETE FROM nbp_prijedlozi_termin
          where oib_pacijenta=:oib and id_pretrage=:id_pretrage');
      $st->execute(array( 'oib'=>$oib, 'id_pretrage'=>$id_pretrage));
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
  }

  function newterminprijedlog($novi){
    try
    {
      $db = DB::getConnection();
      $st = $db->prepare('INSERT INTO nbp_prijedlozi_termin values (:a,:b,:c,:d,:e)');
      $st->execute(array( 'a' => $novi->__get('oib_pacijenta'), 'b' => $novi->__get('id_pretrage'),
        'c' => $novi->__get('datum'), 'd' => $novi->__get('vrijeme'), 'e' => $novi->__get('id_bolnice')));
    }
    catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    return;
  }

}
 ?>
