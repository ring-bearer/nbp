<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/lijecnik.class.php';
require_once __DIR__ . '/bolnica.class.php';

class BolnicaService{
	function getbolnice(){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT id,ime,adresa,mjesto FROM nbp_bolnica order by id');
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

	function getbolnica($id){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT id,ime,adresa,mjesto FROM nbp_bolnica where id=:id');
			$st->execute(array('id'=>$id));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

			$row = $st->fetch();
			if( $row === false )
				return $row;
			$i=new Bolnica($row['id'],
						$row['ime'],$row['adresa'],
						$row['mjesto']);

		return $i;
	}

	function getsusjedi($id){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT id_bolnice1,id_bolnice2 FROM nbp_susjedi where id_bolnice1=:id');
      $st->execute(array('id'=>$id));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $arr=array();
    while(1){
      $row = $st->fetch();
  		if( $row === false )
  			return $arr;
  		else{
        $arr[]=$row['id_bolnice2'];
		  }
    }
    return $arr;
	}
		// Maknuli smo ovu opciju
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

		// Na kraju smo ipak odlucili da ne postoji brisanje bolnica zbog povezanosti sa susjednim bolnicama
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

		function getBolniceByMjesto($mjesto){
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare("select * from nbp_bolnica where udaljenost(CAST ($mjesto AS text), mjesto) < 75;");
				$st->execute();
			}
			catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

			$arr=array();
			while(1){
			$row = $st->fetch();
				if( $row === false )
					return $arr;
				else{
				$i=new Bolnica($row['id'], $row['ime'], $row['adresa'], $row['mjesto']);
				$arr[]=$i;

				}
			}
			return $arr;
		}

		function getTermin($ime_bolnice, $vrsta){
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare("select * FROM prvi_termin(CAST ($ime_bolnice AS text), CAST ($vrsta AS text))");
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
					$i[]=$row['vrijeme'];
					$arr[]=$i;
				}
			}
			return $arr;
		}
	}
