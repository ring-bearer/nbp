<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/pacijent.class.php';

class PacijentService{

	function getpacijenti(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib,mbo,ime,prezime FROM nbp_pacijent order by prezime,ime');
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
  					$row['ime'],$row['prezime']);
        $arr[]=$i;
      }
    }
    return $arr;
	}

	function newpacijent($novi){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('INSERT INTO nbp_pacijent values (:a,:b,:c,:d)');
      $st->execute(array( 'a' => $novi->__get('oib'), 'b' => $novi->__get('mbo'),
				'c' => $novi->__get('ime'), 'd' => $novi->__get('prezime')));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    return;
	}
};

?>
