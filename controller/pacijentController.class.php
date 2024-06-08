<?php

require_once __DIR__ . '/../model/pacijentservice.class.php';
require_once __DIR__ . '/../model/zahtjevservice.class.php';
require_once __DIR__ . '/../model/bolnicaservice.class.php';
require_once __DIR__ . '/../model/terminservice.class.php';
require_once __DIR__ . '/../model/pretragaservice.class.php';
require_once __DIR__ . '/../model/pacijent.class.php';
require_once __DIR__ . '/../model/zahtjev.class.php';
require_once __DIR__ . '/../model/termin.class.php';

class PacijentController{
	public function index(){
		$ls=new PacijentService();
		$list = $ls->getpacijenti();
	 	require_once __DIR__ . '/../view/pacijenti.php';
	}

	public function unos(){
		  require_once __DIR__ . '/../view/newpacijent.php';
	}

	public function transfer(){
		$ls=new PacijentService();
		$zs=new ZahtjevService();
		$pac = $ls->getpacijent($_POST['transfer']);
		$z=new Zahtjev($pac->__get('oib'),$pac->__get('oib_lijecnika'),$_COOKIE['oib']);
		$pac->__set('oib_lijecnika', $_COOKIE['oib']);
		$ls->updatepacijent($pac);

		$zs->deletezahtjev($z);
		$poruka="Zahtjev uspješno prihvaćen!<br>";
		require_once __DIR__ . '/../view/mojizahtjevi.php';
	}

	public function new(){
			$us=new PacijentService();

			if(!preg_match('/^[0-9]{11}$/', $_POST["oib"])){
				$poruka="Unesite ispravan OIB (11 znamenki).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if(!preg_match('/^[0-9]{9}$/', $_POST["mbo"])){
				$poruka="Unesite ispravan MBO (9 znamenki).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["ime"])){
				$poruka="Unesite ispravno ime (0-20 slova).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["prezime"])){
				$poruka="Unesite ispravno prezime (0-20 slova).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if($_POST["pass1"]!==$_POST["pass2"]){
				$poruka="Unesite istu lozinku oba puta! \n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ0-9]{0,30}$/', $_POST["adresa"])){
				$poruka="Unesite ispravnu adresu (0-30 znakova).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["mjesto"])){
				$poruka="Unesite ispravno mjesto (0-20 slova).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if(isset($_POST['oib_lijecnika']))
				$oib_lijecnika=$_POST['oib_lijecnika'];
			else $oib_lijecnika=$_COOKIE['oib'];


			$bs=new Pacijent($_POST['oib'],$_POST['mbo'],
					$_POST['ime'],$_POST['prezime'],
					$_POST['datum_rodjenja'],$_POST['adresa'],
					$_POST['mjesto'],$oib_lijecnika);
			$poruka=$us->newpacijent($bs, $_POST['pass1']);

			require_once __DIR__ . '/../view/_header.php';
			require_once __DIR__ . '/../view/newpacijent.php';
	}

	public function promjena(){
			$ls=new PacijentService();
			$list = $ls->getpacijenti();
			require_once __DIR__ . '/../view/updatepacijent.php';
	}

	public function update(){
			$ls=new PacijentService();

			$list = $ls->getpacijenti();
      foreach ($list as $k=>$l) {

				if(!preg_match('/^[0-9]{9}$/', $_POST["mbo"][$k])){
					$poruka="Unesite ispravan MBO (9 znamenki).\n";
					require_once __DIR__ . '/../view/updatepacijent.php';
					return;
				}
				$l->__set('mbo',$_POST["mbo"][$k]);

          if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["ime"][$k])){
    				$poruka="Unesite ispravno ime (0-20 slova).\n";
    				require_once __DIR__ . '/../view/updatepacijent.php';
    				return;
    			}
          $l->__set('ime',$_POST["ime"][$k]);


  			if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["prezime"][$k])){
  				$poruka="Unesite ispravno prezime (0-20 slova).\n";
  				require_once __DIR__ . '/../view/updatepacijent.php';
  				return;
  			}
        $l->__set('prezime',$_POST["prezime"][$k]);

        $l->__set('datum_rodjenja',$_POST["datum_rodjenja"][$k]);

        if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ0-9]{0,30}$/', $_POST["adresa"][$k])){
  				$poruka="Unesite ispravnu adresu (0-30 znakova).\n";
  				require_once __DIR__ . '/../view/updatepacijent.php';
  				return;
  			}
        $l->__set('adresa',$_POST["adresa"][$k]);

  			if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["mjesto"][$k])){
  				$poruka="Unesite ispravno mjesto (0-20 slova).\n";
  				require_once __DIR__ . '/../view/updatepacijent.php';
  				return;
  			}
        $l->__set('mjesto',$_POST["mjesto"][$k]);

				if(!preg_match('/^[0-9]{11}$/', $_POST["oib_lijecnika"][$k])){
					$poruka="Unesite ispravan OIB (11 znamenki).\n";
					require_once __DIR__ . '/../view/updatepacijent.php';
					return;
				}

				$d=new LijecnikService();
				if($d->getlijecnik($_POST["oib_lijecnika"][$k]))
        	$l->__set('oib_lijecnika',$_POST["oib_lijecnika"][$k]);
				else{
					$poruka="Ne postoji liječnik s tim OIB-om!\n";
					require_once __DIR__ . '/../view/updatepacijent.php';
				}

        $ls->updatepacijent($l);
      }

			foreach ($_POST['brisanje'] as $i) {
				$ls->deletepacijent($i);
			}

			$poruka="Promjene uspješno spremljene!";
			require_once __DIR__ . '/../view/updatepacijent.php';
	}

	public function prijedlozitermin(){
		$ts=new TerminService();
		$ps=new PretragaService();
		$bs=new BolnicaService();
		$pretrage=$ps->getpretrage();
		$list=array();
		$bolnice=array();
		$j=0;
		foreach($pretrage as $a){
			$list[$j]=$ts->getprijedlozitermin($_COOKIE['oib'],$a->__get('id'));
			foreach($list[$j] as $b){
				$id_bolnice=$b->__get('id_bolnice');
				$bolnice[]=$bs->getbolnica($id_bolnice);
			}
			$j++;
		}

		if(empty($bolnice)){
			$poruka="Nemate prijedloga za termine!\n";
			$prazno=1;
		}

		require_once __DIR__ . '/../view/prijedlozitermin.php';
	}

	public function termin(){
		$ts=new TerminService();
		$t=new Termin($_POST['oib'],$_POST['id_pretrage'],
			$_POST['datum'],$_POST['vrijeme'],$_POST['id_bolnice']);
		var_dump($t);
		$ts->deleteprijedlozitermin($_POST['oib'],$_POST['id_pretrage']);
		$ts->newtermin($t);

		$ps=new PretragaService();
		$bs=new BolnicaService();
		$pretrage=$ps->getpretrage();
		$list=array();
		$bolnice=array();
		$j=0;
		foreach($pretrage as $a){
			$list[$j]=$ts->getprijedlozitermin($_COOKIE['oib'],$a->__get('id'));
			foreach($list[$j] as $b){
				$id_bolnice=$b->__get('id_bolnice');
				$bolnice[]=$bs->getbolnica($id_bolnice);
			}
			$j++;
		}
		$poruka="Termin uspješno zakazan! Možete ga vidjeti na popisu naručenih pretraga.\n";
		require_once __DIR__ . '/../view/prijedlozitermin.php';
	}

	public function pretraga(){
		foreach ($_POST as $key => $value) {
			if (strpos($key, 'prihvati_') === 0) {
				//echo "prihvati";
				$index = str_replace('prihvati_', '', $key);

				$oib_pacijenta = $_POST['oib_pacijenta_' . $index];
				$oib_lijecnika = $_POST['oib_lijecnika_' . $index];
				$vrsta = $_POST['vrsta_pretrage_' . $index];
				$mjesto = $_POST['mjesto_' . $index];

				$ls=new LijecnikService();
				$doc=$ls->getlijecnik($oib_lijecnika);
				$id_bolnice=$doc->__get('id_bolnice');

				$bs=new BolnicaService();
				$susjedi=$bs->getsusjedi($id_bolnice);

				$ps=new PretragaService();
				$pretraga=$ps->getpretraga($vrsta);
				$id_pretrage=$pretraga->__get('id');

				$termini=array();
				foreach ($susjedi as $a){
					$t=array();
					if($bs->ispretraga($a,$id_pretrage)==false){
						continue;
					}
					$t=$bs->prvitermin($a,$vrsta);
					$termini[]=new Termin($oib_pacijenta,$id_pretrage,$t[0],$t[1],$a);
				}

				$ts=new TerminService();
				foreach ($termini as $a){
					$ts->newterminprijedlog($a);
				}

				$poruka="Prijedlozi za termine poslani pacijentu!\n";
				$pacs = new PacijentService;
				$pacs->deletePretraga($oib_pacijenta, $oib_lijecnika, $vrsta);

				$list = $ps->mojipretragazahtjevi($_COOKIE['oib']);
				if(empty($list)){
					$poruka= "Prijedlozi za termine poslani pacijentu!\n Nemate zahtjeva na čekanju!";
					$prazno=1;
					require_once __DIR__ . '/../view/pretragazahtjevi.php';
					return;
				}
				foreach($list as $a){
						$oib_pacijenta=$a[0];
						$pacijent=$pacs->getpacijent($oib_pacijenta);
						$listapac[]=$pacijent;
					}
				require_once __DIR__ . '/../view/pretragazahtjevi.php';
			}

				/*// Smislit kako dohvatit najblize bolnice po mjestu pacijenta
				$bs = new BolnicaService;
				$bolnice = $bs->getBolniceByMjesto($mjesto);

				// Moramo pronaci sve termine u njima za odredenu pretragu
				$termini = array();
				foreach($bolnice as $b){
					$ime_bolnice = $b->__get('ime');
					$dostupantermin = $bs->getTermin($ime_bolnice, $vrsta);
					$termini[]=$dostupantermin;
				}

				var_dump($termini);
				// Sada te termine treba poslati pacijentu negdje


				// Na kraju obrisemo taj zahtjev

				header("Location: index.php?rt=pretraga/mojizahtjevi");
				exit();
			}*/
			elseif (strpos($key, 'odbij_') === 0) {
				//echo "Odbij";
				// Izvadimo indeks
				$index = str_replace('odbij_', '', $key);

				$oib_pacijenta = $_POST['oib_pacijenta_' . $index];
				$oib_lijecnika = $_POST['oib_lijecnika_' . $index];
				$vrsta = $_POST['vrsta_pretrage_' . $index];

				$ps = new PacijentService;
				$ps->deletePretraga($oib_pacijenta, $oib_lijecnika, $vrsta);

				$poruka="Zahtjev uspješno odbijen!\n";

				$ps=new PretragaService();
				$pacs=new PacijentService();
				$list = $ps->mojipretragazahtjevi($_COOKIE['oib']);
				if(empty($list)){
					$poruka= "Zahtjev uspješno odbijen!\n Nemate zahtjeva na čekanju!";
					$prazno=1;
					require_once __DIR__ . '/../view/pretragazahtjevi.php';
					return;
				}
				foreach($list as $a){
						$oib_pacijenta=$a[0];
						$pacijent=$pacs->getpacijent($oib_pacijenta);
						$listapac[]=$pacijent;
					}
				require_once __DIR__ . '/../view/pretragazahtjevi.php';
			}
		}
	}
};

?>
