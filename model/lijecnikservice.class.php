<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/lijecnik.class.php';
require_once __DIR__ . '/zahtjev.class.php';

class LijecnikService{
	function getlijecnici(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib,ime,prezime,datum_rodjenja,adresa_ambulante,mjesto_ambulante,id_bolnice FROM nbp_lijecnik order by prezime,ime');
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
						$row['mjesto_ambulante'],$row['id_bolnice']);
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
				'e' => $novi->__get('adresa_ambulante'), 'f' => $novi->__get('mjesto_ambulante'),
				'g' => $novi->__get('id_bolnice')));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    return;
	}

	function getlijecnik($oib){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib,ime,prezime,datum_rodjenja,adresa_ambulante,mjesto_ambulante,id_bolnice from nbp_lijecnik where oib=:oib');
			$st->execute(array( 'oib' => $oib ));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false ) return NULL;

		$i=new Lijecnik($row['oib'],
				$row['ime'],$row['prezime'],
				$row['datum_rodjenja'],$row['adresa_ambulante'],
				$row['mjesto_ambulante'],$row['id_bolnice']);
    return $i;
	}

	function getizbolnice($mjesto_ambulante){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib,ime,prezime,datum_rodjenja,adresa_ambulante,mjesto_ambulante,id_bolnice from nbp_lijecnik where mjesto_ambulante=:mjesto_ambulante');
			$st->execute(array( 'mjesto_ambulante' => $mjesto_ambulante ));
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
						$row['mjesto_ambulante'],$row['id_bolnice']);
        $arr[]=$i;

		  }
    }
    return $arr;
	}

	function deletelijecnik($oib){
		try
		{
			$oib=(string)$oib;
			$db = DB::getConnection();
			$st = $db->prepare('DELETE FROM nbp_lijecnik where oib=:oib');
			$st->execute(array('oib'=>$oib));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() );}
		return;
	}

	function updatelijecnik($novi){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('UPDATE nbp_lijecnik set ime=:b,prezime=:c,datum_rodjenja=:d,adresa_ambulante=:e,mjesto_ambulante=:f,id_bolnice=:g where oib=:oib');
			$st->execute(array('oib' => $novi->__get('oib'), 'b' => $novi->__get('ime'),
				'c' => $novi->__get('prezime'), 'd' => $novi->__get('datum_rodjenja'),
				'e' => $novi->__get('adresa_ambulante'), 'f' => $novi->__get('mjesto_ambulante'),
				'g' => $novi->__get('id_bolnice')));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}
};
