<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/lijecnik.class.php';
require_once __DIR__ . '/pacijent.class.php';
require_once __DIR__ . '/admin.class.php';

class ProfilService{

    function dohvatiUsera($oib, $ovlasti)
    {
        try {
            $db = DB::getConnection();
            switch ($ovlasti) {
                case '0':
                    $st = $db->prepare('SELECT ime FROM nbp_lijecnik WHERE oib=:oib');
                    $st->execute(array('oib' => $oib));
                    break;
                case '1':
                    $st = $db->prepare('SELECT ime FROM nbp_pacijent WHERE oib=:oib');
                    $st->execute(array('oib' => $oib));
                    break;
                case '2':
                    $st = $db->prepare('SELECT ime FROM nbp_admin WHERE oib=:oib');
                    $st->execute(array('oib' => $oib));
                    break;
                default:
                    throw new Exception("Invalid 'ovlasti' value.");
            }

            $row = $st->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                throw new Exception("Ne postoji user sa tim oibom: $oib.");
            }

            $ime = $row['ime'];

            return $ime;

        } catch (PDOException $e) {
            exit('PDO error ' . $e->getMessage());
        } catch (Exception $e) {
            exit('Error: ' . $e->getMessage());
        }
            
    }

    function getProfilLijecnik($oib)
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib,ime,prezime,datum_rodjenja,adresa_ambulante,mjesto_ambulante, id_bolnice FROM nbp_lijecnik where oib=:oib');
            $st->execute(array('oib' => $oib));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

        $row = $st->fetch();
    
        $new=new Lijecnik($row['oib'],
                    $row['ime'],$row['prezime'],
                        $row['datum_rodjenja'],$row['adresa_ambulante'],
                        $row['mjesto_ambulante'], $row['id_bolnice']);

        return $new;
                
    }

    function getProfilPacijent($oib)
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib, mbo, ime, prezime, datum_rodjenja, adresa, mjesto, oib_lijecnika FROM nbp_pacijent where oib=:oib');
            $st->execute(array('oib'=>$oib));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

        $row = $st->fetch();
    
        $new=new Pacijent($row['oib'], $row['mbo'],
                $row['ime'],$row['prezime'],
                $row['datum_rodjenja'],$row['adresa'],
                $row['mjesto'], $row['oib_lijecnika']);

        return $new;
                
    }

    function getProfilAdmin($oib)
    {
        try
		{
			$db = DB::getConnection();
			$st = $db->prepare('SELECT oib,ime,prezime FROM nbp_admin where oib=:oib');
            $st->execute(array('oib' => $oib));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

        $row = $st->fetch();
    
        $new=new Admin($row['oib'],
                    $row['ime'],$row['prezime']);

        return $new;
                
    }
	
};
