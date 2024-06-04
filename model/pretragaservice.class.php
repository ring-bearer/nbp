<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/pretraga.class.php';

class PretragaService{
	/*function getpretrage(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib_pacijenta, vrsta, datum, vrijeme, id_bolnice FROM nbp_pretraga order by datum,vrijeme');
      $st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr=array();
    while(1){
      $row = $st->fetch();
  		if( $row === false )
  			return $arr;
  		else{
        $i=new Pretraga($row['oib_pacijenta'],
  					$row['vrsta'],$row['datum'],
						$row['vrijeme'], $row['id_bolnice'],);
        $arr[]=$i;

		  }
    }
    return $arr;
	}*/

	function povijestpretraga($oib){

		try{
			$db = DB::getConnection();
			$st = $db->prepare("select * from povijestt(CAST ($oib AS text) )");
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
