<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/doktor.class.php';

class DoktorService{
	function getdoktori(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib,ime,prezime,id_bolnica,placa,podrucje,specijalizant FROM nbp_doktor order by prezime');
      $st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr=array();
    while(1){
      $row = $st->fetch();
  		if( $row === false )
  			return $arr;
  		else{
				if($row['specijalizant']) $spec="da";
				else $spec="ne";
        $i=new Doktor($row['oib'],
  					$row['ime'],$row['prezime'],
						$row['id_bolnica'],$row['placa'],
						$row['podrucje'],$spec);
        $arr[]=$i;

		  }
    }
    return $arr;
	}

	function newdoktor($novi){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('INSERT INTO nbp_doktor values (:a,:b,:c,:d,:e,:f,:g)');
      $st->execute(array( 'a' => $novi->__get('oib'), 'b' => $novi->__get('ime'),
				'c' => $novi->__get('prezime'), 'd' => $novi->__get('id_bolnica'),
				'e' => $novi->__get('placa'), 'f' => $novi->__get('podrucje'),
				'g' => $novi->__get('specijalizant')));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    return;
	}
};
