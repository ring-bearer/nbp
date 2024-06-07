<?php

require_once __DIR__ . '/../model/pretragaservice.class.php';
require_once __DIR__ . '/../model/pacijentservice.class.php';
require_once __DIR__ . '/../model/lijecnikservice.class.php';

class PretragaController{
  public function index(){
      $ls=new PretragaService();
      $list = $ls->getpretrage();
			require_once __DIR__ . '/../view/pretrage.php';
	}

  public function allpovijest(){
    $ls=new PretragaService();
    $ps=new PacijentService();
    $pac=$ps->getpacijenti();
    $list=array();
    foreach ($pac as $a){
      $oib=$a->__get('oib');
      $list[]=$ls->povijestpretraga($oib);
    }
    $poruka="Povijest svih pretraga";
    require_once __DIR__ . '/../view/povijestpretraga.php';
  }

  public function allbuduce(){
    $ls=new PretragaService();
    $ps=new PacijentService();
    $pac=$ps->getpacijenti();
    $list=array();
    foreach ($pac as $a){
      $oib=$a->__get('oib');
      $list[]=$ls->buducepretrage($oib);
    }
    $poruka="Naručene pretrage";
    require_once __DIR__ . '/../view/povijestpretraga.php';
  }

  public function zahtjevi(){
      $ps=new PretragaService();
      $ds=new PacijentService();
      $ls=new LijecnikService();
      $list = $ps->getpretragazahtjevi();
      if(empty($list)){
        $poruka="Nema zahtjeva na čekanju!";
        $prazno=1;
        require_once __DIR__ . '/../view/pretragazahtjevi.php';
        return;
      }
      foreach($list as $a){
          $oib_pacijenta=$a[0];
          $pac=$ds->getpacijent($oib_pacijenta);
          $listapac[]=$pac;
          $oib_lijecnika=$a[1];
          $lijec=$ls->getlijecnik($oib_lijecnika);
          $listalijec[]=$lijec;
        }
      require_once __DIR__ . '/../view/pretragazahtjevi.php';
  }

  public function mojizahtjevi(){
      $ps=new PretragaService();
      $ds=new PacijentService();
      $list = $ps->mojipretragazahtjevi($_COOKIE['oib']);
      if(empty($list)){
        $poruka="Nemate zahtjeva na čekanju!";
        $prazno=1;
        require_once __DIR__ . '/../view/pretragazahtjevi.php';
        return;
      }
      foreach($list as $a){
          $oib_pacijenta=$a[0];
          $pacijent=$ds->getpacijent($oib_pacijenta);
          $listapac[]=$pacijent;
        }
      require_once __DIR__ . '/../view/pretragazahtjevi.php';
  }

  public function povijest(){
      $ls=new PretragaService();
      $list = $ls->povijestpretraga($_COOKIE['oib']);
      $poruka="Povijest mojih pretraga";
      require_once __DIR__ . '/../view/povijestpretraga.php';
	}

  public function buduce(){
      $ls=new PretragaService();
      $list = $ls->buducepretrage($_COOKIE['oib']);
      $poruka="Naručene pretrage";
      require_once __DIR__ . '/../view/povijestpretraga.php';
  }

  public function unos(){
		  require_once __DIR__ . '/../view/newpretraga.php';
	}

  public function new(){
    $ls=new PacijentService();
    $oib=$_COOKIE['oib'];
    $pac = $ls->getpacijent($oib);

    $oib_lijecnika=$pac->__get('oib_lijecnika');
    $ps=new PretragaService();
    if($ps->getpretragazahtjev($oib,$oib_lijecnika,$_POST['zahtjev'])!==0){
      $poruka= "Ovaj zahtjev je već poslan!";
  		require_once __DIR__ . '/../view/newpretraga.php';
      return;
    }
    else{
      $ps->newzahtjev($oib,$oib_lijecnika,$_POST['zahtjev']);
      $poruka="Zahtjev uspješno poslan!";
  		require_once __DIR__ . '/../view/newpretraga.php';
    }
	}


};

?>
