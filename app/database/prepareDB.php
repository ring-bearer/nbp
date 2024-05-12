<?php

require_once 'db.class.php';

$db = DB::getConnection();

try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_bolnica (
          id int not null,
          ime character varying(20) not null,
          --ovo su GPS koordinate
          zemlj_sirina float
              constraint chkSirina
                  check (zemlj_sirina >= -90 and zemlj_sirina<=90),
          zemlj_duzina float
              constraint chkDuzina
                  check (zemlj_duzina >= -180 and zemlj_duzina<=180),
          constraint pkBolnica primary key (id)
          );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_bolnica: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_bolnica.<br>";

try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_doktor (
          oib char(11) not null,
          ime character varying(20) not null,
          prezime character varying(20) not null,
          id_bolnica int,
          placa int,
          podrucje character varying(20), --ono za sto je specijaliziran
          specijalizant boolean, --0 ako je vec zavrsio spec, inace 1
          constraint pkDoktor primary key (oib),
          constraint fkBolnica foreign key (id_bolnica) references nbp_bolnica(id)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_doktor: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_doktor.<br>";


try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_pacijent (
          oib char(11) not null,
          mbo char(9), --maticni broj osiguranika, ako pacijent ima zdravstveno
          ime character varying(20) not null,
          prezime character varying(20) not null,
          constraint pkPacijent primary key (oib)
          );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error kod nbp_pacijent: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_pacijent.<br>";

try
{
    $st = $db->prepare( 'INSERT INTO nbp_bolnica(id, ime, zemlj_sirina, zemlj_duzina) VALUES (:id, :ime, :sirina, :duzina)' );

    $st->execute( array( 'id' => 01, 'ime' => 'Minas Tirith', 'sirina' => 0, 'duzina' => 0 ) );
    $st->execute( array( 'id' => 02, 'ime' => 'Edoras', 'sirina' => -5, 'duzina' => -40 ) );
    $st->execute( array( 'id' => 03, 'ime' => 'Rivendell', 'sirina' => -50, 'duzina' => -5 ) );
    $st->execute( array( 'id' => 04, 'ime' => 'Mirkwood', 'sirina' => -55, 'duzina' => 60 ) );
    $st->execute( array( 'id' => 05, 'ime' => 'Bree', 'sirina' => -55, 'duzina' => -10 ) );
    $st->execute( array( 'id' => 06, 'ime' => 'Numenor', 'sirina' => -40, 'duzina' => 60 ) );
    $st->execute( array( 'id' => 07, 'ime' => 'Tirion', 'sirina' => -80, 'duzina' => -150 ) );

}

catch( PDOException $e ) { exit( "PDO error kod bolnice: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_bolnica.<br />";

try
{
    $st = $db->prepare( 'INSERT INTO nbp_doktor(oib,ime,prezime,id_bolnica,placa,podrucje,specijalizant) VALUES (:oib,:ime,:prezime,:id_bolnica,:placa,:podrucje,:spec )');

    $st->execute( array( 'oib' => '10000444444', 'ime' => 'Gandalf', 'prezime' => 'Mithrandir', 'id_bolnica' => 05, 'placa' => 5000, 'podrucje' => 'neonatologija', 'spec' => 0) );
    $st->execute( array( 'oib' => '10000291105', 'ime' => 'Aragorn', 'prezime' => 'Elessar', 'id_bolnica' => 01, 'placa' => 2000, 'podrucje' => 'hematologija', 'spec' => 1) );
    $st->execute( array( 'oib' => '10000062905', 'ime' => 'Legolas', 'prezime' => 'Thranduilion', 'id_bolnica' => 04, 'placa' => 4000, 'podrucje' => 'oftalmologija', 'spec' => 0) );
    $st->execute( array( 'oib' => '10000999919', 'ime' => 'Theoden', 'prezime' => 'Ednew', 'id_bolnica' => 02, 'placa' => 2000, 'podrucje' => 'fizikalna medicina', 'spec' => 1) );
    $st->execute( array( 'oib' => '10000857999', 'ime' => 'Arwen', 'prezime' => 'Undomiel', 'id_bolnica' => 03, 'placa' => 5000, 'podrucje' => 'dermatologija', 'spec' => 0) );
    $st->execute( array( 'oib' => '10000893743', 'ime' => 'Denethor', 'prezime' => 'Ecthelion', 'id_bolnica' => 01, 'placa' => 2000, 'podrucje' => 'kirurgija', 'spec' => 1) );
    $st->execute( array( 'oib' => '10000891233', 'ime' => 'Tom', 'prezime' => 'Bombadil', 'id_bolnica' => 05, 'placa' => 6000, 'podrucje' => 'psihijatrija', 'spec' => 0) );
    $st->execute( array( 'oib' => '10000213905', 'ime' => 'Elendil', 'prezime' => 'Voronda', 'id_bolnica' => 06, 'placa' => 5000, 'podrucje' => 'hematologija', 'spec' => 0) );
    $st->execute( array( 'oib' => '10000294736', 'ime' => 'Feanor', 'prezime' => 'Curufinwe', 'id_bolnica' => 07, 'placa' => 6500, 'podrucje' => 'kirurgija', 'spec' => 0) );
    $st->execute( array( 'oib' => '10000243905', 'ime' => 'Maedhros', 'prezime' => 'Nelyafinwe', 'id_bolnica' => 07, 'placa' => 3000, 'podrucje' => 'fizikalna medicina', 'spec' => 1) );
    $st->execute( array( 'oib' => '10000432043', 'ime' => 'Finwe', 'prezime' => 'Noldoran', 'id_bolnica' => 07, 'placa' => 6000, 'podrucje' => 'neonatologija', 'spec' => 0) );
    $st->execute( array( 'oib' => '10000794735', 'ime' => 'Elrond', 'prezime' => 'Peredhel', 'id_bolnica' => 03, 'placa' => 5000, 'podrucje' => 'oftalmologija', 'spec' => 0) );

}
catch( PDOException $e ) { exit( "PDO error kod doktora: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_doktor.<br />";

try
{
    $st = $db->prepare( 'INSERT INTO nbp_pacijent(oib, mbo, ime, prezime) VALUES (:oib, :mbo, :ime, :prezime)' );

    $st->execute( array( 'oib' => '10000338099', 'mbo' => '100338099', 'ime' => 'Frodo', 'prezime' => 'Baggins') );
    $st->execute( array( 'oib' => '10000917906', 'mbo' => '100917906', 'ime' => 'Samwise', 'prezime' => 'Gamgee') );
    $st->execute( array( 'oib' => '10000998713', 'mbo' => '100998713', 'ime' => 'Meriadoc', 'prezime' => 'Brandybuck') );
    $st->execute( array( 'oib' => '10000395731', 'mbo' => '100395731', 'ime' => 'Peregrin', 'prezime' => 'Took') );
    $st->execute( array( 'oib' => '10000520909', 'mbo' => '100520909', 'ime' => 'Boromir', 'prezime' => 'Echtelion') );
    $st->execute( array( 'oib' => '10000013006', 'mbo' => '100013006', 'ime' => 'Faramir', 'prezime' => 'Echtelion') );
    $st->execute( array( 'oib' => '10000878383', 'mbo' => '100878383', 'ime' => 'Eowyn', 'prezime' => 'Eadig') );
    $st->execute( array( 'oib' => '10000402929', 'mbo' => '100402929', 'ime' => 'Eomer', 'prezime' => 'Eadig') );

}
catch( PDOException $e ) { exit( "PDO error kod pacijenata: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_pacijent.<br />";
?>
