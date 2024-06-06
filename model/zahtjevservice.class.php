<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/lijecnik.class.php';
require_once __DIR__ . '/zahtjev.class.php';

class ZahtjevService{
    function getallzahtjevi(){
      try
      {
        $db = DB::getConnection();
        $st = $db->prepare('SELECT oib_pacijenta,oib_stari,oib_novi FROM nbp_zahtjev');
        $st->execute();
      }
      catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

      $arr=array();
      while(1){
        $row = $st->fetch();
        if( $row === false )
          return $arr;
        else{
          $i=new Zahtjev($row['oib_pacijenta'],
              $row['oib_stari'],$row['oib_novi']);
          $arr[]=$i;

        }
      }
      return $arr;
    }

  	function getzahtjevi($oib){
  		try
  		{
  			$db = DB::getConnection();
  			$st = $db->prepare('SELECT oib_pacijenta,oib_stari FROM nbp_zahtjev where oib_novi=:oib');
        $st->execute(array('oib'=>$oib));
  		}
  		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

      $arr=array();
      while(1){
        $row = $st->fetch();
    		if( $row === false )
    			return $arr;
    		else{
          $i=new Zahtjev($row['oib_pacijenta'],
    					$row['oib_stari'],$oib);
          $arr[]=$i;

  		  }
      }
      return $arr;
  	}

    function newzahtjev($novi){
  	try
  		{
  			$db = DB::getConnection();

  			$st = $db->prepare('INSERT INTO nbp_zahtjev values (:a,:b,:c)');
        $st->execute(array( 'a' => $novi->__get('oib_pacijenta'), 'b' => $novi->__get('oib_stari'),
  				'c' => $novi->__get('oib_novi')));
  		}
  		catch( PDOException $e ) {
        if($st->errorCode()==='23505'){
          $poruka="Ovaj zahtjev je već postavljen!";
          return $poruka;
        }
        exit( 'PDO error ' . $e->getMessage() ); }

  		$poruka="Zahtjev uspjesno postavljen!";
      return $poruka;
  	}

    function deletezahtjev($z){
  		try
  		{
  			$db = DB::getConnection();
  			$st = $db->prepare('DELETE FROM nbp_zahtjev where oib_pacijenta=:oib_pacijenta and oib_stari=:stari and oib_novi=:novi;');
  			$st->execute(array('oib_pacijenta'=>$z->__get('oib_pacijenta'), 'stari'=>$z->__get('oib_stari'), 'novi'=>$z->__get('oib_novi')));
  		}
  		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
  	}

}
