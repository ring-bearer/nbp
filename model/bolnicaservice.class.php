<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/lijecnik.class.php';
require_once __DIR__ . '/bolnica.class.php';

class BolnicaService{
	function getbolnice(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT id,ime,adresa,mjesto FROM nbp_bolnica order by ime');
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
  					$row['ime'],$row['adresa'],
						$row['mjesto']);
        $arr[]=$i;

		  }
    }
    return $arr;
	}

		function newbolnica($novi){
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare('INSERT INTO nbp_bolnica values (default,:a,:b,:c)');
	      $st->execute(array( 'a' => $novi->__get('ime'), 'b' => $novi->__get('adresa'),
					'c' => $novi->__get('mjesto')));
				$st = $db->prepare('SELECT id from nbp_bolnica where ime=:a');
				$st->execute(array( 'a' => $novi->__get('ime')));
			}
			catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

			$row=$st->fetch();
	    return $row;
		}

		function deletebolnica($id){
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare('DELETE FROM nbp_bolnica where id=:id');
				$st->execute(array('id'=>$id));
			}
			catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		}

		function updatebolnica($novi){
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare('UPDATE nbp_bolnica set ime=:b,adresa=:c,mjesto=:d where id=:id');
				$st->execute(array('id' => $novi->__get('id'), 'b' => $novi->__get('ime'),
					'c' => $novi->__get('adresa'), 'd' => $novi->__get('mjesto')));
			}
			catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		}
	}