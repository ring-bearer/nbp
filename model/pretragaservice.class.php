<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/pretraga.class.php';

class PretragaService{
	function getpretrage(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT id, vrsta, trajanje_min from nbp_pretraga');
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

	function getpretragabyid($id){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT id, vrsta, trajanje_min from nbp_pretraga where id=:id');
      $st->execute(array('id'=>$id));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

      $row = $st->fetch();
  		if( $row === false )
  			return 0;
  		else{
        $i=new Pretraga($row['id'],
  					$row['vrsta'],$row['trajanje_min']);
    }
    return $i;
	}

	function getpretraga($vrsta){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT id, vrsta, trajanje_min from nbp_pretraga where vrsta=:vrsta');
      $st->execute(array('vrsta'=>$vrsta));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

      $row = $st->fetch();
  		if( $row === false )
  			return 0;
  		else{
        $i=new Pretraga($row['id'],
  					$row['vrsta'],$row['trajanje_min']);
    }
    return $i;
	}

		function getpretragazahtjev($pac,$lijec,$vrsta){
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare('SELECT oib_pacijenta, oib_lijecnika, vrsta from nbp_zahtjev_pretraga where oib_pacijenta=:pac and oib_lijecnika=:lijec and vrsta=:vrsta');
	      $st->execute(array( 'pac' => $pac, 'lijec' => $lijec,
					'vrsta' => $vrsta));
			}
			catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

	      $row = $st->fetch();
	  		if( $row === false )
	  			return 0;
	  		else{
	        $i=array();
					$i[]=$row['oib_pacijenta'];
					$i[]=$row['oib_lijecnika'];
					$i[]=$row['vrsta'];
			    return $i;
			  }
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


	function getpretragazahtjevi(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib_pacijenta, oib_lijecnika, vrsta from nbp_zahtjev_pretraga');
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
				$i[]=$row['oib_pacijenta'];
				$i[]=$row['oib_lijecnika'];
				$i[]=$row['vrsta'];
        $arr[]=$i;

		  }
    }
    return $arr;
	}

	function mojipretragazahtjevi($oib){
	try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib_pacijenta, oib_lijecnika, vrsta from nbp_zahtjev_pretraga where oib_lijecnika=:oib');
			$st->execute( array('oib' => $oib) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr=array();
		while(1){
			$row = $st->fetch();
			if( $row === false )
				return $arr;
			else{
				$i=array();
				$i[]=$row['oib_pacijenta'];
				$i[]=$row['oib_lijecnika'];
				$i[]=$row['vrsta'];
				$arr[]=$i;
			}
		}
		return $arr;
	}

	function povijestpretraga($oib){

		try{
			$db = DB::getConnection();
			$st = $db->prepare("select * from povijest_pretraga(CAST ($oib AS text)) where datum < CURRENT_DATE;");
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
				$i[]=$row['vrijeme'];
        $arr[]=$i;
		  }
    }
		return $arr;

	}

	function buducepretrage($oib){

		try{
			$db = DB::getConnection();
			$st = $db->prepare("select * from povijest_pretraga(CAST ($oib AS text)) where datum >= CURRENT_DATE;");
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
