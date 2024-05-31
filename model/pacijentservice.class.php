<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/pacijent.class.php';
require_once __DIR__ . '/lijecnikservice.class.php';

class PacijentService{

	function getpacijenti(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib, mbo, ime, prezime, datum_rodjenja, adresa, mjesto, oib_lijecnika
				FROM nbp_pacijent order by prezime');
      $st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr=array();
    while(1){
      $row = $st->fetch();
  		if( $row === false )
  			return $arr;
  		else{
        $i=new Pacijent($row['oib'],$row['mbo'],
  					$row['ime'],$row['prezime'],$row['datum_rodjenja'],
						$row['adresa'],$row['mjesto'],$row['oib_lijecnika']);
        $arr[]=$i;
      }
    }
    return $arr;
	}

	function getpacijent($oib){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib, mbo, ime, prezime, datum_rodjenja, adresa, mjesto, oib_lijecnika
				FROM nbp_pacijent where oib=:oib');
      $st->execute(array('oib'=>$oib));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false ) return NULL;

		$i=new Pacijent($row['oib'], $row['mbo'],
				$row['ime'],$row['prezime'],
				$row['datum_rodjenja'],$row['adresa'],
				$row['mjesto'], $row['oib_lijecnika']);
    return $i;
	}

	function getmojipacijenti($oib_lijecnika){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib, mbo, ime, prezime, datum_rodjenja, adresa, mjesto, oib_lijecnika
				FROM nbp_pacijent where oib_lijecnika=:oib_lijecnika order by prezime');
      $st->execute(array('oib_lijecnika'=>$oib_lijecnika));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr=array();
    while(1){
      $row = $st->fetch();
  		if( $row === false )
  			return $arr;
  		else{
        $i=new Pacijent($row['oib'],$row['mbo'],
  					$row['ime'],$row['prezime'],$row['datum_rodjenja'],
						$row['adresa'],$row['mjesto'],$row['oib_lijecnika']);
        $arr[]=$i;
      }
    }
    return $arr;
	}

	function newpacijent($novi, $pass){
	try
		{
			$db = DB::getConnection();

			$poruka="";
			$ls=new LijecnikService();
			$lijecnik=$ls->getlijecnik($novi->__get('oib_lijecnika'));
			if($lijecnik===NULL){
				$poruka="Ne postoji lijecnik s tim OIB-om!";
				return $poruka;
			}

			$st = $db->prepare('INSERT INTO nbp_pacijent values (:a,:pass,:b,:c,:d,:e,:f,:g,:h)');
      $st->execute(array( 'a' => $novi->__get('oib'), 'pass'=> $pass, 'b' => $novi->__get('mbo'),
				'c' => $novi->__get('ime'), 'd' => $novi->__get('prezime'),
				'e' => $novi->__get('datum_rodjenja'), 'f' => $novi->__get('adresa'),
				'g' => $novi->__get('mjesto'), 'h' => $novi->__get('oib_lijecnika'),));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$poruka="Pacijent uspjesno dodan!";
    return $poruka;
	}


	function deletepacijent($oib){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('DELETE FROM nbp_pacijent where oib=:oib');
			$st->execute(array('oib'=>$oib));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	function updatepacijent($novi){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('UPDATE nbp_pacijent set mbo=:a,ime=:b,prezime=:c,datum_rodjenja=:d,adresa=:e,mjesto=:f,oib_lijecnika=:g where oib=:oib');
			$st->execute(array('oib' => $novi->__get('oib'), 'a' => $novi->__get('mbo'), 'b' => $novi->__get('ime'),
				'c' => $novi->__get('prezime'), 'd' => $novi->__get('datum_rodjenja'),
				'e' => $novi->__get('adresa'), 'f' => $novi->__get('mjesto'), 'g' => $novi->__get('oib_lijecnika')));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}
};

?>
