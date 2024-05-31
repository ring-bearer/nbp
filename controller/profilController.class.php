<?php

require_once __DIR__ . '/../model/profilservice.class.php';
require_once __DIR__ . '/../model/lijecnik.class.php';

class ProfilController{
  public function index(){
      
        $oib = $_COOKIE['oib'];
        $ovlasti = $_COOKIE['ovlasti'];

        //echo $ovlasti;

        $ps = new ProfilService;

        $ime = $ps->dohvatiUsera($oib, $ovlasti);

        require_once __DIR__ . '/../view/profil.php';

	}

};

?>
