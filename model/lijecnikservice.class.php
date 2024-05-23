<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/lijecnik.class.php';

class LijecnikService{
	function getlijecnici(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib,ime,prezime,datum_rodjenja,adresa_ambulante,mjesto_ambulante FROM nbp_lijecnik order by prezime,ime');
      $st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr=array();
    while(1){
      $row = $st->fetch();
  		if( $row === false )
  			return $arr;
  		else{
        $i=new Lijecnik($row['oib'],
  					$row['ime'],$row['prezime'],
						$row['datum_rodjenja'],$row['adresa_ambulante'],
						$row['mjesto_ambulante']);
        $arr[]=$i;

		  }
    }
    return $arr;
	}

	function newlijecnik($novi){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('INSERT INTO nbp_lijecnik values (:a,:b,:c,:d,:e,:f,:g)');
      $st->execute(array( 'a' => $novi->__get('oib'), 'b' => $novi->__get('ime'),
				'c' => $novi->__get('prezime'), 'd' => $novi->__get('datum_rodjenja'),
				'e' => $novi->__get('adresa_ambulante'), 'f' => $novi->__get('mjesto_ambulante')));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    return;
	}

	function getlijecnik($oib){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib,ime,prezime from nbp_lijecnik where oib=:oib');
			$st->execute(array( 'oib' => $oib ));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false ) return NULL;

		$i=new Lijecnik($row['oib'],
				$row['ime'],$row['prezime'],
				$row['datum_rodjenja'],$row['adresa_ambulante'],
				$row['mjesto_ambulante']);
    return $i;
	}
};
