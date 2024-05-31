<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/lijecnik.class.php';

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
                    $st = $db->prepare('SELECT ime FROM nbp_admini WHERE oib=:oib');
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
	
};
