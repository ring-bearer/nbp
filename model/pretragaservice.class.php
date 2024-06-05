<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/pretraga.class.php';

class PretragaService{
	function getpretrage(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT id, vrsta, trajanje_min');
      $st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr=array();
    while(1){
      $row = $st->fetch();
  		if( $row === false )
  			return $arr;
  		else{
        $i=new Pretraga($row['id'],
  					$row['vrsta'],$row['trajanje_min']);
        $arr[]=$i;

		  }
    }
    return $arr;
	}

	function newzahtjev($oib,$oib_lijecnika,$vrsta){
	try
		{
			$db = DB::getConnection();

			$st = $db->prepare('INSERT INTO nbp_zahtjev_pretraga values (:a,:b,:c)');
      $st->execute(array( 'a' => $oib, 'b' => $oib_lijecnika,
				'c' => $vrsta));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		return;
	}

	function povijestpretraga($oib){

		try{
			$db = DB::getConnection();
			$st = $db->prepare("select * from povijest_pretraga(CAST ($oib AS text) )");
			$st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr=array();
    while(1){
      $row = $st->fetch();
  		if( $row === false )
  			return $arr;
  		else{
        $i=array();
				$i[]=$row['datum'];
				$i[]=$row['vrsta'];
				$i[]=$row['ime_bolnice'];
        $arr[]=$i;

		  }
    }
		return $arr;

	}
};
