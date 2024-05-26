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

	function newpacijent($novi){
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

			$st = $db->prepare('INSERT INTO nbp_pacijent values (:a,:b,:c,:d,:e,:f,:g,:h)');
      $st->execute(array( 'a' => $novi->__get('oib'), 'b' => $novi->__get('mbo'),
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
};

?>
