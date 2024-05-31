<?php

require_once __DIR__ . '/../model/lijecnikservice.class.php';
require_once __DIR__ . '/../model/pacijentservice.class.php';
require_once __DIR__ . '/../model/zahtjevservice.class.php';
require_once __DIR__ . '/../model/lijecnik.class.php';
require_once __DIR__ . '/../model/pacijent.class.php';
require_once __DIR__ . '/../model/zahtjev.class.php';

class ZahtjevController{
  public function index(){
      $zs=new ZahtjevService();
      $ps=new PacijentService();
      $ls=new LijecnikService();
      $list = $zs->getallzahtjevi();
      if(empty($list)){
        $poruka="Nema zahtjeva na čekanju!";
        $prazno=1;
        require_once __DIR__ . '/../view/zahtjevi.php';
        return;
      }
      foreach($list as $a){
          $oib_pacijenta=$a->__get('oib_pacijenta');
          $pac=$ps->getpacijent($oib_pacijenta);
          $listapac[]=$pac;
          $oib_stari=$a->__get('oib_stari');
          $lijec=$ls->getlijecnik($oib_stari);
          $listastarih[]=$lijec;
          $oib_novi=$a->__get('oib_novi');
          $lijec=$ls->getlijecnik($oib_novi);
          $listanovih[]=$lijec;
        }
			require_once __DIR__ . '/../view/zahtjevi.php';
	}

  public function novi(){
    $ls=new LijecnikService();
    $ps=new PacijentService();
    $tren=$ls->getlijecnik($_COOKIE['oib']);
    $mjesto_ambulante=$tren->__get('mjesto_ambulante');
    $lijeclist=$ls->getizbolnice($mjesto_ambulante);
    $list=$ps->getmojipacijenti($_COOKIE['oib']);
    require_once __DIR__ . '/../view/newzahtjev.php';
  }

  public function new(){
    $zs=new ZahtjevService();
    $z=new Zahtjev($_POST['pacijent'],$_COOKIE['oib'],$_POST['novi']);
    $poruka=$zs->newzahtjev($z);
    $ls=new LijecnikService();
    $ps=new PacijentService();
    $tren=$ls->getlijecnik($_COOKIE['oib']);
    $mjesto_ambulante=$tren->__get('mjesto_ambulante');
    $lijeclist=$ls->getizbolnice($mjesto_ambulante);
    $list=$ps->getmojipacijenti($_COOKIE['oib']);
    require_once __DIR__ . '/../view/newzahtjev.php';
  }

    public function mojizahtjevi(){
      $ls=new LijecnikService();
      $zs=new ZahtjevService();
      $list = $zs->getzahtjevi($_COOKIE['oib']);
      if(empty($list)){
        $poruka="Nemate zahtjeva na čekanju!";
        $prazno=1;
        require_once __DIR__ . '/../view/mojizahtjevi.php';
        return;
      }
      $ps=new PacijentService();
      $listapac=array();
      foreach($list as $a){
        $oib_pacijenta=$a->__get('oib_pacijenta');
        $pac=$ps->getpacijent($oib_pacijenta);
        $listapac[]=$pac;
        $oib_stari=$a->__get('oib_stari');
        $lijec=$ls->getlijecnik($oib_stari);
        $listalijec[]=$lijec;
      }
      require_once __DIR__ . '/../view/mojizahtjevi.php';
    }


}
