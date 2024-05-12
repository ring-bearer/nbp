<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/bolnica.class.php';

class BolnicaService{
	function getbolnice(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT id,ime,zemlj_sirina,zemlj_duzina FROM nbp_bolnica order by ime');
      $st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr=array();
    while(1){
      $row = $st->fetch();
  		if( $row === false )
  			return $arr;
  		else{
        $i=new Bolnica($row['id'],
  					$row['ime'],$row['zemlj_sirina'],
						$row['zemlj_duzina']);
        $arr[]=$i;

		  }
    }
    return $arr;
	}
};
