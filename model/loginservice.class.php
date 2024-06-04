<?php

require_once __DIR__ . '/../app/database/db.class.php';

// Ovlasti:
// 0 - lijecnik
// 1 - pacijent
// 2 - admin

class LoginService{

    public function userprovjeraPacijent($oib)
    {
  
        $db = DB::getConnection();

        try
        {
            $st2 = $db->prepare( 'SELECT password_hash FROM nbp_pacijent WHERE oib=:oib' );
            $st2->execute( array( 'oib' => $oib ) );
        }
        catch( PDOException $e ) { require_once __DIR__ . '/../view/login.php'; echo 'Greska u bazi 2.';return; }

        $row = $st2->fetch();

        $ovlasti = 1;

        if( $row === false ){
            // Taj user ne postoji, upit u bazu nije vratio ništa.
            require_once __DIR__ . '/../view/loginpacijent.php';
            echo 'Ne postoji korisnik s tim imenom.';
            return;
        }
        else{
            require_once __DIR__ . '../../controller/profilController.class.php';
            $od=new ProfilController();
            $od->index();
            return 1;
        }
  
    }

    public function userprovjeraLijecnik($oib)
    {
  
        $db = DB::getConnection();
  
        // Ovaj dio se moze optimizirati sa query
        try
        {
            $st1 = $db->prepare( 'SELECT password_hash FROM nbp_lijecnik WHERE oib=:oib' );
            $st1->execute( array( 'oib' => $oib ) );
        }
        catch( PDOException $e ) { require_once __DIR__ . '/../view/login.php'; echo 'Greska u bazi 1.';return; }
    
        $row = $st1->fetch();

        $ovlasti = 0;

        if( $row === false ){
            // Taj user ne postoji, upit u bazu nije vratio ništa.
            require_once __DIR__ . '/../view/loginlijecnik.php';
            echo 'Ne postoji korisnik s tim imenom.';
            return;
        }
        else{
            require_once __DIR__ . '../../controller/profilController.class.php';
            $od=new ProfilController();
            $od->index();
            return 1;
        }
  
    }


    public function userprovjeraAdmin($oib)
    {
  
        $db = DB::getConnection();
  
        try
        {
            $st3 = $db->prepare( 'SELECT password_hash FROM nbp_admin WHERE oib=:oib' );
            $st3->execute( array( 'oib' => $oib ) );
        }
        catch( PDOException $e ) { require_once __DIR__ . '/../view/login.php'; echo 'Greska u bazi 31.';return; }


        $row = $st3->fetch();

        $ovlasti = 2;
        

        if( $row === false ){
            // Taj user ne postoji, upit u bazu nije vratio ništa.
            require_once __DIR__ . '/../view/loginadmin.php';
            echo 'Ne postoji korisnik s tim imenom.';
            return;
        }
        else{
            require_once __DIR__ . '../../controller/profilController.class.php';
            $od=new ProfilController();
            $od->index();
            return 1;
        }
  
    }
  
    public function provjeraUBaziPacijent($oib, $password)
    {

        $db = DB::getConnection();

        try
        {
            $st2 = $db->prepare( 'SELECT password_hash FROM nbp_pacijent WHERE oib=:oib' );
            $st2->execute( array( 'oib' => $oib ) );
        }
        catch( PDOException $e ) { require_once __DIR__ . '/../view/loginpacijent.php'; echo 'Greska u bazi 2.';return; }

        $row = $st2->fetch();

        $ovlasti = 1;

        if( $row === false )
        {
            // Taj user ne postoji, upit u bazu nije vratio ništa.
            require_once __DIR__ . '/../view/loginpacijent.php';
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
                if(isset($_COOKIE['oib'])){
                    return 1;
                }

                // Dobar password. Ulogiraj ga i posalji na pocetni ekran.
                // Moramo dohvatiti i njegove ovlasti
                setcookie('oib',$oib,time()+(10*365*24*60*60));
                setcookie('ovlasti',$ovlasti,time()+(10*365*24*60*60));

                // Ova linija je potrebna da se cookie zapamti pri prvom ulasku na stranicu
                header("Location: index.php");

                require_once __DIR__ . '../../controller/profilController.class.php';
                $od=new ProfilController();
                $od->index();
                return 1;
            }
            else{

                // Nije dobar password. Crtaj opet login formu s pripadnom porukom.
                require_once __DIR__ . '/../view/loginpacijent.php';
                echo 'Postoji user, ali password nije dobar.';
                return 0;
            }
        }

    }

    public function provjeraUBaziLijecnik($oib, $password)
    {

        $db = DB::getConnection();

        // Ovaj dio se moze optimizirati sa query
        try
        {
            $st = $db->prepare( 'SELECT password_hash FROM nbp_lijecnik WHERE oib=:oib' );
            $st->execute( array( 'oib' => $oib ) );
        }
        catch( PDOException $e ) { require_once __DIR__ . '/../view/loginlijecnik.php'; echo 'Greska u bazi 1.';return; }

        $row = $st->fetch();

        $ovlasti = 0;

        if( $row === false )
        {
            // Taj user ne postoji, upit u bazu nije vratio ništa.
            require_once __DIR__ . '/../view/loginlijecnik.php';
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
                if(isset($_COOKIE['oib'])){
                    return 1;
                }

                // Dobar password. Ulogiraj ga i posalji na pocetni ekran.
                // Moramo dohvatiti i njegove ovlasti
                setcookie('oib',$oib,time()+(10*365*24*60*60));
                setcookie('ovlasti',$ovlasti,time()+(10*365*24*60*60));

                // Ova linija je potrebna da se cookie zapamti pri prvom ulasku na stranicu
                header("Location: index.php");

                require_once __DIR__ . '../../controller/profilController.class.php';
                $od=new ProfilController();
                $od->index();
                return 1;
            }
            else{

                // Nije dobar password. Crtaj opet login formu s pripadnom porukom.
                require_once __DIR__ . '/../view/loginlijecnik.php';
                echo 'Postoji user, ali password nije dobar.';
                return 0;
            }
        }

    }

    public function provjeraUBaziAdmin($oib, $password)
    {

        $db = DB::getConnection();

        try
        {
            $st3 = $db->prepare( 'SELECT password_hash FROM nbp_admin WHERE oib=:oib' );
            $st3->execute( array( 'oib' => $oib ) );
        }
        catch( PDOException $e ) { require_once __DIR__ . '/../view/loginadmin.php'; echo 'Greska u bazi 32.';return; }

        $row = $st3->fetch();

        $ovlasti = 2;
        
        if( $row === false )
        {
            // Taj user ne postoji, upit u bazu nije vratio ništa.
            require_once __DIR__ . '/../view/loginadmin.php';
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
                if(isset($_COOKIE['oib'])){
                    return 1;
                }

                // Dobar password. Ulogiraj ga i posalji na pocetni ekran.
                // Moramo dohvatiti i njegove ovlasti
                setcookie('oib',$oib,time()+(10*365*24*60*60));
                setcookie('ovlasti',$ovlasti,time()+(10*365*24*60*60));

                // Ova linija je potrebna da se cookie zapamti pri prvom ulasku na stranicu
                header("Location: index.php");

                require_once __DIR__ . '../../controller/profilController.class.php';
                $od=new ProfilController();
                $od->index();
                return 1;
            }
            else{

                // Nije dobar password. Crtaj opet login formu s pripadnom porukom.
                require_once __DIR__ . '/../view/loginadmin.php';
                echo 'Postoji user, ali password nije dobar.';
                return 0;
            }
        }

    }
	
};

?>
