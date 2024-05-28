<?php

require_once __DIR__ . '/../app/database/db.class.php';

class LoginService{

    public function userprovjera($oib)
    {
  
        $db = DB::getConnection();
  
        try
        {
            $st = $db->prepare( 'SELECT password_hash FROM nbp_lijecnici WHERE oib=:oib' );
            $st->execute( array( 'oib' => $oib ) );
        }
        catch( PDOException $e ) { require_once __DIR__ . '/../view/login.php'; echo 'Greska u bazi.';return; }
    
        $row = $st->fetch();

        if( $row === false ){
            // Taj user ne postoji, upit u bazu nije vratio ništa.
            require_once __DIR__ . '/../view/login.php';
            echo 'Ne postoji korisnik s tim imenom.';
            return;
        }
        else{
            require_once __DIR__ . '../../controller/pacijentController.class.php';
            $od=new PacijentController();
            $od->index();
            return 1;
        }
  
    }
  
    public function provjeraUBazi($oib, $password)
    {

        $db = DB::getConnection();

        try
        {
            $st = $db->prepare( 'SELECT password_hash FROM nbp_lijecnik WHERE oib=:oib' );
            $st->execute( array( 'oib' => $oib ) );
        }
        catch( PDOException $e ) { require_once __DIR__ . '/../view/login.php'; echo 'Greska u bazi.';return; }

        $row = $st->fetch();

        if( $row === false )
        {
            // Taj user ne postoji, upit u bazu nije vratio ništa.
            require_once __DIR__ . '/../view/login.php';
            echo 'Ne postoji korisnik s tim imenom.';
            return 0;
        }
        else
        {

            // Postoji user. Dohvati hash njegovog passworda.
            $hash = $row[ 'password_hash'];

            // Da li je password dobar?
            if( password_verify( $password, $hash ))
            {
                //ako je korisnik ulogiran od prije
                //znaci da je ovo provjera pri mijenjanju sifre
                if(isset($_COOKIE['username'])){
                return 1;
                }

                // Dobar password. Ulogiraj ga i posalji na pocetni ekran.
                // Moramo dohvatiti i njegove ovlasti
                setcookie('oib',$oib,time()+(10*365*24*60*60));

                // Ova linija je potrebna da se cookie zapamti pri prvom ulasku na stranicu
                //header("Location: index.php");

                require_once __DIR__ . '../../controller/pacijentController.class.php';
                $od=new PacijentController();
                $od->index();
                return 1;
            }
            else{

                // Nije dobar password. Crtaj opet login formu s pripadnom porukom.
                require_once __DIR__ . '/../view/login.php';
                echo 'Postoji user, ali password nije dobar.';
                return 0;
            }
        }

    }
	
};

?>
