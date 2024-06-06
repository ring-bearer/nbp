<?php

require_once 'db.class.php';

$db = DB::getConnection();

try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_bolnica(
            id serial check (not null),
            ime character varying(100) check (not null),
            adresa character varying(50) check (not null),
            mjesto character varying(20) check (not null),
            constraint pkbBolnica primary key (id)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_bolnica: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_bolnica.<br>";

try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_mjesto(
            naziv character varying(25) check (not null),
            gs numeric check (not null), -- znamo da je N
            gd numeric check (not null), -- znamo da je E
            constraint pkMjesto primary key (naziv)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_mjesto: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_mjesto.<br>";


//zahtjev za novom pretragom koji pacijent salje svom lijecniku
try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_zahtjev_pretraga(
            oib_pacijenta char(11) check (not null),
            oib_lijecnika char(11) check (not null),
            vrsta char varying(30) check (not null),
            constraint pkZahtjeviPretraga primary key (oib_pacijenta,oib_lijecnika,vrsta)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_zahtjev_pretraga: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_zahtjev_pretraga.<br>";


try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_pretraga(
            id serial check (not null),
            vrsta character varying(30) check (not null),
            trajanje_min int check (not null), -- ukljucuje ciscenje nakon pretrage i kratku pauzu ako je potrebno
            constraint pkPretraga primary key (id)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_pretraga: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_pretraga.<br>";

try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_bolnica_pretraga(
            id_bolnice int check (not null),
            id_pretrage int check (not null),
            constraint pkBP primary key (id_bolnice, id_pretrage),
            constraint fkBolnica foreign key (id_bolnice) references nbp_bolnica(id),
            constraint fkPretraga foreign key (id_pretrage) references nbp_pretraga(id)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_bolnica_pretraga: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_bolnica_pretraga.<br>";

try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_susjedi(
            id_bolnice1 int check (not null),
            id_bolnice2 int check (not null),
            constraint pkSusjedi primary key (id_bolnice1, id_bolnice2),
            constraint fkBolnica1 foreign key (id_bolnice1) references nbp_bolnica(id),
            constraint fkBolnica2 foreign key (id_bolnice2) references nbp_bolnica(id)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_susjedi: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_susjedi.<br>";

try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_admin(
            oib char(11) not null, --neznam kako ubacit ovaj check da ne javlja error
            ime character varying(20) check (not null),
            prezime character varying(20) check (not null),
            password_hash varchar(255) check (not null), -- lozinka za prijavu u sustav
            constraint pk_Admin primary key (oib)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_admin: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_admin.<br>";

try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_lijecnik(
            oib char(11) not null,
            ime character varying(20) check (not null),
            prezime character varying(20) check (not null),
            datum_rodjenja date check (not null),
            adresa_ambulante character varying(30) check (not null),
            mjesto_ambulante character varying(20) check (not null),
            password_hash varchar(255) check (not null), -- lozinka za prijavu u sustav
            constraint pkLijecnik primary key (oib)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_lijecnik: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_lijecnik.<br>";


try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_pacijent(
            oib char(11) not null,
            mbo char(9), --maticni broj osiguranika, ako pacijent ima zdravstveno
            ime character varying(20) check (not null),
            prezime character varying(20) check (not null),
            datum_rodjenja date check (not null),
            adresa character varying(30) check (not null), -- trenutna adresa boravista
            mjesto character varying(20) check (not null), -- trenutno mjesto boravista -> na temelju toga racunamo udaljenost od bolnica
            oib_lijecnika char(11) not null, -- oib lijecnika opce prakse
            password_hash varchar(255) check (not null), -- lozinka za prijavu u sustav
            constraint pkPacijent primary key (oib),
            constraint fkPacijent foreign key (oib_lijecnika) references nbp_lijecnik(oib) ON DELETE CASCADE
        );'
      );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error kod nbp_pacijent: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_pacijent.<br>";

try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_termin(
            oib_pacijenta char(11) not null,
            id_pretrage int check (not null),
            datum date check (not null),
            vrijeme time check (not null),
            id_bolnice int check (not null),
            constraint pkTermin primary key (oib_pacijenta, datum, vrijeme),
            constraint fkPacijent foreign key (oib_pacijenta) references nbp_pacijent(oib) ON DELETE CASCADE,
            constraint fkPretraga foreign key (id_pretrage) references nbp_pretraga(id),
            constraint fkBolnica foreign key (id_bolnice) references nbp_bolnica(id)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_termin: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_termin.<br>";


// zahtjevi za prebacivanjem pacijenta kod drugog liječnika
try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_zahtjev(
          oib_pacijenta char(11) check (not null),
          oib_stari char(11) check (not null), --trenutni liječnik
          oib_novi char(11) check (not null), --željeni novi liječnik
          constraint pkZahtjevi primary key (oib_pacijenta,oib_stari,oib_novi)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_zahtjev: " . $e->getMessage() ); }

echo "Napravio tablicu zahtjev.<br>";

//---------------------------------------------------------------------

//-- indeksi za ubrzavanje upita

try
{
    $st = $db->prepare('CREATE INDEX IF NOT EXISTS pacijent_ime_idx ON nbp_pacijent(prezime, ime);');
    $st->execute();

    $st = $db->prepare('CREATE INDEX IF NOT EXISTS termin_pacijent_idx ON nbp_termin(oib_pacijenta);');
    $st->execute();

}
catch( PDOException $e ) { exit( "PDO error za indexe: " . $e->getMessage() ); }

echo "Napravio indexe.<br>";


/////////punjenje tablica

try
{
    $st = $db->prepare( 'INSERT INTO nbp_lijecnik(oib,ime,prezime,datum_rodjenja,adresa_ambulante,mjesto_ambulante,password_hash) VALUES (:oib,:ime,:prezime,:datum_rodjenja,:adresa_ambulante,:mjesto_ambulante,:password)');

    $st->execute( array( 'oib' => '10000444444', 'ime' => 'Gandalf', 'prezime' => 'Mithrandir', 'datum_rodjenja' => '1940-07-02', 'adresa_ambulante' => 'Glavna 8', 'mjesto_ambulante' => 'Bree', 'password' => password_hash( 'gandalf123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000291105', 'ime' => 'Aragorn', 'prezime' => 'Elessar', 'datum_rodjenja' => '1980-07-02', 'adresa_ambulante' => 'Glavna 1', 'mjesto_ambulante' => 'Minas Tirith', 'password' => password_hash( 'aragorn123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000062905', 'ime' => 'Legolas', 'prezime' => 'Thranduilion', 'datum_rodjenja' => '1970-07-02', 'adresa_ambulante' => 'Glavna 7', 'mjesto_ambulante' => 'Mirkwood', 'password' => password_hash( 'legolas123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000999919', 'ime' => 'Theoden', 'prezime' => 'Ednew', 'datum_rodjenja' => '1960-07-02', 'adresa_ambulante' => 'Glavna 5', 'mjesto_ambulante' => 'Bree', 'password' => password_hash( 'theoden123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000857999', 'ime' => 'Arwen', 'prezime' => 'Undomiel', 'datum_rodjenja' => '1970-07-02', 'adresa_ambulante' => 'Glavna 4', 'mjesto_ambulante' => 'Rivendell', 'password' => password_hash( 'arwen123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000893743', 'ime' => 'Denethor', 'prezime' => 'Ecthelion', 'datum_rodjenja' => '1950-07-02', 'adresa_ambulante' => 'Glavna 9', 'mjesto_ambulante' => 'Minas Tirith', 'password' => password_hash( 'denethor123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000891233', 'ime' => 'Tom', 'prezime' => 'Bombadil', 'datum_rodjenja' =>'1940-07-02', 'adresa_ambulante' => 'Glavna 2', 'mjesto_ambulante' => 'Bree', 'password' => password_hash( 'tom123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000213905', 'ime' => 'Elendil', 'prezime' => 'Voronda', 'datum_rodjenja' => '1960-07-02', 'adresa_ambulante' => 'Glavna 11', 'mjesto_ambulante' => 'Numenor', 'password' => password_hash( 'elendil123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000294736', 'ime' => 'Feanor', 'prezime' => 'Curufinwe', 'datum_rodjenja' => '1960-07-02', 'adresa_ambulante' => 'Glavna 12', 'mjesto_ambulante' => 'Tirion', 'password' => password_hash( 'feanor123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000243905', 'ime' => 'Maedhros', 'prezime' => 'Nelyafinwe', 'datum_rodjenja' => '1980-07-02', 'adresa_ambulante' => 'Glavna 6', 'mjesto_ambulante' => 'Tirion', 'password' => password_hash( 'maedhro123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000432043', 'ime' => 'Finwe', 'prezime' => 'Noldoran', 'datum_rodjenja' => '1940-07-02', 'adresa_ambulante' => 'Glavna 13', 'mjesto_ambulante' => 'Tirion', 'password' => password_hash( 'finwe123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000794735', 'ime' => 'Elrond', 'prezime' => 'Peredhel', 'datum_rodjenja' => '1940-07-02', 'adresa_ambulante' => 'Glavna 3', 'mjesto_ambulante' => 'Rivendell', 'password' => password_hash( 'elrond123', PASSWORD_DEFAULT )) );

}
catch( PDOException $e ) { exit( "PDO error kod lijecnika: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_lijecnik.<br />";

try
{
    $st = $db->prepare( 'INSERT INTO nbp_pacijent(oib, mbo, ime, prezime, datum_rodjenja, adresa, mjesto, oib_lijecnika, password_hash) VALUES (:oib, :mbo, :ime, :prezime, :datum_rodjenja, :adresa, :mjesto, :oib_lijecnika, :password)' );

    $st->execute( array( 'oib' => '10000338099', 'mbo' => '100338099', 'ime' => 'Frodo', 'prezime' => 'Baggins', 'datum_rodjenja' => '2000-07-02', 'adresa' => 'Glavna 1', 'mjesto' => 'Hobbiton', 'oib_lijecnika' => '10000444444', 'password' => password_hash( 'frodo123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000917906', 'mbo' => '100917906', 'ime' => 'Samwise', 'prezime' => 'Gamgee', 'datum_rodjenja' => '1999-07-02', 'adresa' => 'Glavna 3', 'mjesto' => 'Hobbiton', 'oib_lijecnika' => '10000891233', 'password' => password_hash( 'samwise123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000998713', 'mbo' => '100998713', 'ime' => 'Meriadoc', 'prezime' => 'Brandybuck', 'datum_rodjenja' => '1998-07-02', 'adresa' => 'Glavna 2', 'mjesto' => 'Buckland', 'oib_lijecnika' => '10000891233', 'password' => password_hash( 'meriadoc123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000395731', 'mbo' => '100395731', 'ime' => 'Peregrin', 'prezime' => 'Took', 'datum_rodjenja' => '2001-07-02', 'adresa' => 'Glavna 4', 'mjesto' => 'Buckland', 'oib_lijecnika' => '10000444444', 'password' => password_hash( 'peregrin123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000520909', 'mbo' => '100520909', 'ime' => 'Boromir', 'prezime' => 'Echtelion', 'datum_rodjenja' => '1980-07-02', 'adresa' => 'Glavna 5', 'mjesto' => 'Minas Tirith', 'oib_lijecnika' => '10000291105', 'password' => password_hash( 'boromir123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000013006', 'mbo' => '100013006', 'ime' => 'Faramir', 'prezime' => 'Echtelion', 'datum_rodjenja' => '1990-07-02', 'adresa' => 'Glavna 5', 'mjesto' => 'Minas Tirith', 'oib_lijecnika' => '10000893743', 'password' => password_hash( 'faramir123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000878383', 'mbo' => '100878383', 'ime' => 'Eowyn', 'prezime' => 'Eadig', 'datum_rodjenja' => '1990-07-02', 'adresa' => 'Glavna 7', 'mjesto' => 'Edoras', 'oib_lijecnika' => '10000999919', 'password' => password_hash( 'eowyn123', PASSWORD_DEFAULT )) );
    $st->execute( array( 'oib' => '10000402929', 'mbo' => '100402929', 'ime' => 'Eomer', 'prezime' => 'Eadig', 'datum_rodjenja' => '1990-07-02', 'adresa' => 'Glavna 7', 'mjesto' => 'Edoras', 'oib_lijecnika' => '10000999919', 'password' => password_hash( 'eomer123', PASSWORD_DEFAULT )) );

}
catch( PDOException $e ) { exit( "PDO error kod pacijenata: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_pacijent.<br />";

try
{
    $st = $db->prepare( 'INSERT INTO nbp_admin(oib, ime, prezime, password_hash) VALUES (:oib, :ime, :prezime, :password)' );

    $st->execute( array( 'oib' => '10000338023', 'ime' => 'Nora', 'prezime' => 'Berdalović', 'password' => password_hash( 'admin123', PASSWORD_DEFAULT )) );

}
catch( PDOException $e ) { exit( "PDO error kod admina: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_admin.<br />";


try
{
    $st = $db->prepare( 'INSERT INTO nbp_zahtjev(oib_pacijenta, oib_stari, oib_novi) VALUES (:oib, :stari, :novi)' );

    $st->execute( array( 'oib' => '10000395731', 'stari' => '10000444444', 'novi' => '10000891233') );

}
catch( PDOException $e ) { exit( "PDO error kod zahtjeva: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_zahtjev.<br />";




try
{
    $st = $db->prepare( 'INSERT INTO nbp_bolnica(id, ime, adresa, mjesto) VALUES (default, :ime, :adresa, :mjesto)' );

    $st->execute( array( 'ime' => 'Opća bolnica "Dr. Ivo Pedišić" Sisak', 'adresa' => 'J.J. Strossmayera 59', 'mjesto' => 'Sisak') ); $st->execute( array( 'ime' => 'Opća bolnica "Dr. Josip Benčević" Slavonski Brod', 'adresa' => 'Andrije Štampara 42', 'mjesto' => 'Slavonski Brod') ); $st->execute( array( 'ime' => 'Opća bolnica "Dr. Tomislav Bardek" Koprivnica', 'adresa' => 'Željka Selingera bb', 'mjesto' => 'Koprivnica') ); $st->execute( array( 'ime' => 'Opća bolnica "Hrvatski ponos" Knin', 'adresa' => 'Svetoslava Suronje 12', 'mjesto' => 'Knin') ); $st->execute( array( 'ime' => 'Opća bolnica Bjelovar', 'adresa' => 'Mihanovićeva 8', 'mjesto' => 'Bjelovar') ); $st->execute( array( 'ime' => 'Opća bolnica Dubrovnik', 'adresa' => 'Roka Mišetića 2', 'mjesto' => 'Dubrovnik') ); $st->execute( array( 'ime' => 'Opća bolnica Gospić', 'adresa' => 'Kaniška 111', 'mjesto' => 'Gospić') ); $st->execute( array( 'ime' => 'Opća bolnica Karlovac', 'adresa' => 'Andrije Štampara 3', 'mjesto' => 'Karlovac') ); $st->execute( array( 'ime' => 'Opća bolnica Ogulin', 'adresa' => 'Bolnička 38', 'mjesto' => 'Ogulin') ); $st->execute( array( 'ime' => 'Opća bolnica Pula', 'adresa' => 'Aldo Negri 6', 'mjesto' => 'Pula') ); $st->execute( array( 'ime' => 'Opća bolnica Šibensko-kninske županije', 'adresa' => 'Stjepana Radića 83', 'mjesto' => 'Šibenik') ); $st->execute( array( 'ime' => 'Opća bolnica Varaždin', 'adresa' => 'I. Meštrovića bb', 'mjesto' => 'Varaždin') ); $st->execute( array( 'ime' => 'Opća bolnica Vinkovci', 'adresa' => 'Zvonarska 57', 'mjesto' => 'Vinkovci') ); $st->execute( array( 'ime' => 'Opća bolnica Virovitica', 'adresa' => 'Ljudevita Gaja 21', 'mjesto' => 'Virovitica') ); $st->execute( array( 'ime' => 'Opća bolnica Zabok', 'adresa' => 'Bračak 8', 'mjesto' => 'Zabok') ); $st->execute( array( 'ime' => 'Opća bolnica Zadar', 'adresa' => 'Bože Peričića 5', 'mjesto' => 'Zadar') ); $st->execute( array( 'ime' => 'Opća županijska bolnica Našice', 'adresa' => 'Bana Jelačića 10', 'mjesto' => 'Našice') ); $st->execute( array( 'ime' => 'Opća županijska bolnica Požega', 'adresa' => 'Osječka 107', 'mjesto' => 'Požega') ); $st->execute( array( 'ime' => 'Opća županijska bolnica Vukovar', 'adresa' => 'Županijska 35', 'mjesto' => 'Vukovar') ); $st->execute( array( 'ime' => 'Županijska bolnica Čakovec', 'adresa' => 'I. G. Kovačića 1E' , 'mjesto' => 'Čakovec') ); $st->execute( array( 'ime' => 'Klinička bolnica "Dubrava"', 'adresa' => 'Avenija Gojka Šuška 6', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Klinička bolnica "Sveti Duh"', 'adresa' => 'Sveti Duh 64', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Klinička bolnica "Merkur"', 'adresa' => 'Zajčeva 19', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Klinika za dječje bolesti', 'adresa' => 'Klaićeva 16', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Klinika za infektivne bolesti "Dr. Fran Mihaljević"', 'adresa' => 'Mirogojska 8', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Klinika za ortopediju Lovran', 'adresa' => 'Šetalište Maršala Tita 1', 'mjesto' => 'Lovran') ); $st->execute( array( 'ime' => 'Klinika za psihijatriju Vrapče', 'adresa' => 'Bolnička cesta 32', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Magdalena - Klinika za kardiovaskularne bolesti Med.fakulteta u Osijeku', 'adresa' => 'Ljudevita Gaja 2', 'mjesto' => 'Krapinske Toplice') ); $st->execute( array( 'ime' => 'Biokovka specijalna bolnica za medicinsku rehabilitaciju - Makarska', 'adresa' => 'Put Cvitačke 9', 'mjesto' => 'Makarska') ); $st->execute( array( 'ime' => 'Kalos Specijalna bolnica za medicinsku rehabilitaciju Vela Luka', 'adresa' => 'Obala 3 br. 3', 'mjesto' => 'Vela Luka') ); $st->execute( array( 'ime' => 'Bolnica za ortopedsku kirurgiju i rehabilitaciju "Prim.dr. Martin Horvat" Rovinj', 'adresa' => 'Ulica Luigi Monti 2', 'mjesto' => 'Rovinj') );
    $st->execute( array( 'ime' => 'Opća bolnica "Dr. Josip Benčević" Slavonski Brod', 'adresa' => 'Andrije Štampara 42', 'mjesto' => 'Slavonski Brod') ); $st->execute( array( 'ime' => 'Dječja bolnica Srebrnjak', 'adresa' => 'Srebrnjak 100', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Neuropsihijatrijska bolnica "Dr. Ivan Barbot" Popovača', 'adresa' => 'Jelengradska 1', 'mjesto' => 'Popovača') ); $st->execute( array( 'ime' => 'Psihijatrijska bolnica "Sveti Ivan"', 'adresa' => 'Jankomir 11', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Psihijatrijska bolnica "Sveti Rafael" Strmac', 'adresa' => 'Šumetlica 87', 'mjesto' => 'Cernik') ); $st->execute( array( 'ime' => 'Psihijatrijska bolnica Lopača', 'adresa' => 'Lopača 11', 'mjesto' => 'Dražice') ); $st->execute( array( 'ime' => 'Psihijatrijska bolnica Rab', 'adresa' => 'Kampor 224', 'mjesto' => 'Rab') ); $st->execute( array( 'ime' => 'Psihijatrijska bolnica Ugljan', 'adresa' => 'Otočkih dragovoljaca 42', 'mjesto' => 'Ugljan') ); $st->execute( array( 'ime' => 'Psihijatrijska bolnica za djecu i mladež', 'adresa' => 'Ivana Kukuljevića 11', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Specijalna bolnica za kronične bolesti dječje dobi Gornja Bistra', 'adresa' => 'Bolnička 21', 'mjesto' => 'Gornja Bistra') ); $st->execute( array( 'ime' => 'Specijalna bolnica za medicinsku rehabilitaciju "Naftalan" Ivanić Grad', 'adresa' => 'Omladinska 23a', 'mjesto' => 'Ivanić Grad') ); $st->execute( array( 'ime' => 'Specijalna bolnica za medicinsku rehabilitaciju Daruvarske Toplice', 'adresa' => 'Julijev park 1', 'mjesto' => 'Daruvar') ); $st->execute( array( 'ime' => 'Specijalna bolnica za medicinsku rehabilitaciju Krapinske Toplice', 'adresa' => 'Gajeva 2', 'mjesto' => 'Krapinske Toplice') ); $st->execute( array( 'ime' => 'Specijalna bolnica za medicinsku rehabilitaciju Lipik', 'adresa' => 'Marije Terezije 13', 'mjesto' => 'Lipik') ); $st->execute( array( 'ime' => 'Specijalna bolnica za medicinsku rehabilitaciju Stubičke Toplice', 'adresa' => 'Park Matije Gupca 1', 'mjesto' => 'Stubičke toplice') ); $st->execute( array( 'ime' => 'Specijalna bolnica za medicinsku rehabilitaciju Varaždinske Toplice', 'adresa' => 'Trg Slobode 1', 'mjesto' => 'Varaždinske Toplice') ); $st->execute( array( 'ime' => 'Specijalna bolnica za ortopediju Biograd na Moru', 'adresa' => 'Zadarska 62', 'mjesto' => 'Biograd na Moru') ); $st->execute( array( 'ime' => 'Specijalna bolnica za plućne bolesti', 'adresa' => 'Rockefellerova 3', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Specijalna bolnica za produženo liječenje - Duga Resa', 'adresa' => 'Jozefa Jeruzalema 7', 'mjesto' => 'Duga Resa') ); $st->execute( array( 'ime' => 'Specijalna bolnica za zaštitu djece s neurorazvojnim i motoričkim smetnjama', 'adresa' => 'Goljak 2', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Specijalna bolnica za medicinsku rehabilitaciju Primorsko-goranske županije', 'adresa' => 'Gajevo šetalište 21', 'mjesto' => 'Crikvenica') ); $st->execute( array( 'ime' => 'Specijalna bolnica za medicinsku rehabilitaciju bolesti srca, pluća i reumatizma', 'adresa' => 'Maršala Tita 188/1', 'mjesto' => 'Opatija') );
    $st->execute( array( 'ime' => 'Poliklinika za bolesti dišnog sustava', 'adresa' => 'Prilaz Baruna Filipovića 11', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Poliklinika za fizikalnu medicinu i rehabilitaciju dr. Drago Čop', 'adresa' => 'Antuna Mihanovića 3', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Poliklinika za prevenciju kardiovaskularnih bolesti i rehabilitaciju', 'adresa' => 'Draškovićeva 13', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Poliklinika za rehabilitaciju slušanja i govora Suvag', 'adresa' => 'Kneza Ljudevita Posavskog 10', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Poliklinika za rehabilitaciju slušanja i govora Suvag', 'adresa' => 'J.J. Strossmayera 6', 'mjesto' => 'Osijek') ); $st->execute( array( 'ime' => 'Poliklinika za rehabilitaciju slušanja i govora Suvag', 'adresa' => 'dr. Vladka Mačeka 48', 'mjesto' => 'Karlovac') ); $st->execute( array( 'ime' => 'Stomatološka poliklinika Zagreb', 'adresa' => 'Perkovčeva 3', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Stomatološka poliklinika Split', 'adresa' => 'A. G. Matoša 2', 'mjesto' => 'Split') ); $st->execute( array( 'ime' => 'Poliklinika za zaštitu djece grada Zagreba', 'adresa' => 'Đorđićeva 26', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Poliklinika za rehabilitaciju osoba sa smetnjama u razvoju Split', 'adresa' => 'Put Meja 5', 'mjesto' => 'Split') ); $st->execute( array( 'ime' => 'Poliklinika za fizikalnu medicinu i rehabilitaciju Velika Gorica', 'adresa' => 'Matice Hrvatske bb', 'mjesto' => 'Velika Gorica') ); $st->execute( array( 'ime' => 'Dom zdravlja Bjelovarsko-bilogorske županije', 'adresa' => 'J. Jelačića 13 c', 'mjesto' => 'Bjelovar') ); $st->execute( array( 'ime' => 'Dom zdravlja Čakovec', 'adresa' => 'I. G. Kovačića 1e', 'mjesto' => 'Čakovec') ); $st->execute( array( 'ime' => 'Dom zdravlja Dubrovnik', 'adresa' => 'Dr. A. Starčevića 1', 'mjesto' => 'Dubrovnik') ); $st->execute( array( 'ime' => 'Dom zdravlja Donji Miholjac', 'adresa' => 'Trg Ante Starčevića 25', 'mjesto' => 'Donji Miholjac') ); $st->execute( array( 'ime' => 'Dom zdravlja Beli Manastir', 'adresa' => 'Školska 5', 'mjesto' => 'Beli Manastir') ); $st->execute( array( 'ime' => 'Dom zdravlja Drniš', 'adresa' => 'Josipa Kosora 14', 'mjesto' => 'Drniš') ); $st->execute( array( 'ime' => 'Dom zdravlja Đakovo', 'adresa' => 'Petra Preradovića 2', 'mjesto' => 'Đakovo') ); $st->execute( array( 'ime' => 'Dom zdravlja Gospić', 'adresa' => '118. brigade HV 3', 'mjesto' => 'Gospić') ); $st->execute( array( 'ime' => 'Dom zdravlja Karlovac', 'adresa' => 'D.V. Mačeka 48', 'mjesto' => 'Karlovac') ); $st->execute( array( 'ime' => 'Dom zdravlja Knin', 'adresa' => 'Kneza I. Nelipića 1', 'mjesto' => 'Knin') ); $st->execute( array( 'ime' => 'Dom zdravlja Koprivničko-križevačke županije', 'adresa' => 'Trg Tomislava Bardeka 10', 'mjesto' => 'Koprivnica') ); $st->execute( array( 'ime' => 'Dom zdravlja Korčula', 'adresa' => 'Ulica 57 k. br. 5', 'mjesto' => 'Korčula') ); $st->execute( array( 'ime' => 'Dom zdravlja Korenica', 'adresa' => 'Zagrebačaka 41', 'mjesto' => 'Korenica') ); $st->execute( array( 'ime' => 'Dom zdravlja Krapinsko-zagorske županije', 'adresa' => 'Dr. M. Crkvenca 1', 'mjesto' => 'Krapina') ); $st->execute( array( 'ime' => 'Dom zdravlja Kutina', 'adresa' => 'A.G. Matoša 42', 'mjesto' => 'Kutina') ); $st->execute( array( 'ime' => 'Dom zdravlja Metković', 'adresa' => 'Dr. A. Starčevića 12', 'mjesto' => 'Metković') ); $st->execute( array( 'ime' => 'Dom zdravlja MUP-a', 'adresa' => 'Šarengradska 3', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Dom zdravlja Našice', 'adresa' => 'Bana Josipa Jelačića 10', 'mjesto' => 'Našice') ); $st->execute( array( 'ime' => 'Dom zdravlja "dr.A.Štampar" Nova Gradiška', 'adresa' => 'Relkovićeva 7', 'mjesto' => 'Nova Gradiška') ); $st->execute( array( 'ime' => 'Dom zdravlja Novalja', 'adresa' => 'Špital 1', 'mjesto' => 'Novalja') ); $st->execute( array( 'ime' => 'Dom zdravlja Ogulin', 'adresa' => 'B.Frankopana 14', 'mjesto' => 'Ogulin') ); $st->execute( array( 'ime' => 'Dom zdravlja Osijek', 'adresa' => 'Park kralja Petra Krešimira IV br. 6', 'mjesto' => 'Osijek') ); $st->execute( array( 'ime' => 'Dom zdravlja Ozalj', 'adresa' => 'Kolodvorska 2', 'mjesto' => 'Ozalj') ); $st->execute( array( 'ime' => 'Dom zdravlja Petrinja', 'adresa' => 'Matije Gupca 4', 'mjesto' => 'Petrinja') ); $st->execute( array( 'ime' => 'Dom zdravlja Ploče', 'adresa' => 'Kralja Tomislava 9', 'mjesto' => 'Ploče') ); $st->execute( array( 'ime' => 'Dom zdravlja Požeško-slavonske županije', 'adresa' => 'M. Gupca 10', 'mjesto' => 'Požega') ); $st->execute( array( 'ime' => 'Dom zdravlja Primorsko-goranske županije', 'adresa' => 'M. Gupca 10', 'mjesto' => 'Požega') ); $st->execute( array( 'ime' => 'Dom zdravlja Požeško-slavonske županije', 'adresa' => 'Krešimirova 52/a', 'mjesto' => 'Rijeka') ); $st->execute( array( 'ime' => 'Dom zdravlja Senj', 'adresa' => 'Stara cesta 43', 'mjesto' => 'Senj') ); $st->execute( array( 'ime' => 'Dom zdravlja Slavonski Brod', 'adresa' => 'Borovska 7', 'mjesto' => 'Slavonski Brod') ); $st->execute( array( 'ime' => 'Dom zdravlja Slunj', 'adresa' => 'Plitvička 18a', 'mjesto' => 'Slunj') ); $st->execute( array( 'ime' => 'Dom zdravlja Splitsko-dalmatinske županije', 'adresa' => 'Kavanjinova 2', 'mjesto' => 'Split') ); $st->execute( array( 'ime' => 'Dom zdravlja Šibenik', 'adresa' => 'Stjepana Radića 83', 'mjesto' => 'Šibenik') ); $st->execute( array( 'ime' => 'Dom zdravlja Valpovo', 'adresa' => 'Kralja Petra Krešimira IV br. 4', 'mjesto' => 'Valpovo') ); $st->execute( array( 'ime' => 'Dom zdravlja Varaždinske županije', 'adresa' => 'Kolodvorska 20', 'mjesto' => 'Varaždin') ); $st->execute( array( 'ime' => 'Dom zdravlja "Dr. A. Franulović" Vela Luka', 'adresa' => 'Ulica 1 br. 1', 'mjesto' => 'Vela Luka') ); $st->execute( array( 'ime' => 'Dom zdravlja Virovitičko-podravske županije', 'adresa' => 'Gajeva 21', 'mjesto' => 'Virovitica') ); $st->execute( array( 'ime' => 'Dom zdravlja Vojnić', 'adresa' => 'A.Hebranga 24', 'mjesto' => 'Vojnić') ); $st->execute( array( 'ime' => 'Dom zdravlja Zagreb - Centar', 'adresa' => 'Runjaninova 4', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Dom zdravlja Zagreb - Zapad', 'adresa' => 'Prilaz baruna Filipovića 11', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Dom zdravlja Zagrebačke županije', 'adresa' => 'Ljudevita Gaja 37', 'mjesto' => 'Samobor') ); $st->execute( array( 'ime' => 'Dom zdravlja Zagreb - Istok', 'adresa' => 'Švarcova 20', 'mjesto' => 'Zagreb') ); $st->execute( array( 'ime' => 'Dom Zadarske županije', 'adresa' => 'Ivana Mažuranića 28a', 'mjesto' => 'Zadar') ); $st->execute( array( 'ime' => 'Istarski domovi zdravlja', 'adresa' => 'Flanatička 27', 'mjesto' => 'Pula') ); $st->execute( array( 'ime' => 'Dom zdravlja Otočac', 'adresa' => 'Vladimira Nazora 14', 'mjesto' => 'Otočac') ); $st->execute( array( 'ime' => 'Dom zdravlja Duga Resa', 'adresa' => 'Bana J. Jelačića 4', 'mjesto' => 'Duga Resa') ); $st->execute( array( 'ime' => 'Dom zdravlja Vinkovci', 'adresa' => 'Kralja Zvonimira 53', 'mjesto' => 'Vinkovci') ); $st->execute( array( 'ime' => 'Dom zdravlja Vukovar', 'adresa' => 'Sajmište 1', 'mjesto' => 'Vukovar') ); $st->execute( array( 'ime' => 'Dom zdravlja Županja', 'adresa' => 'dr. Franje Račkog 32', 'mjesto' => 'Županja') ); $st->execute( array( 'ime' => 'Dom zdravlja Sisak', 'adresa' => 'Kralja Tomislava 1', 'mjesto' => 'Sisak') );
}
catch( PDOException $e ) { exit( "PDO error kod bolnica: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_bolnica.<br />";

try
{
    $st = $db->prepare( 'INSERT INTO nbp_pretraga(id, vrsta, trajanje_min) VALUES (:id, :vrsta, :trajanje_min)' );

    $st->execute( array( 'id' => '1', 'vrsta' => 'dermatološki pregled', 'trajanje_min' => '45') );
    $st->execute( array( 'id' => '2', 'vrsta' => 'oftalmološki pregled', 'trajanje_min' => '45') );
    $st->execute( array( 'id' => '3', 'vrsta' => 'fizikalna medicina', 'trajanje_min' => '45') );
    $st->execute( array( 'id' => '4', 'vrsta' => 'magnetska rezonanca', 'trajanje_min' => '50') );
    $st->execute( array( 'id' => '5', 'vrsta' => 'serologija', 'trajanje_min' => '10') );
    $st->execute( array( 'id' => '6', 'vrsta' => 'dijabetologija', 'trajanje_min' => '30') );

}
catch( PDOException $e ) { exit( "PDO error kod pretraga: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_pretraga.<br />";

try
{
    $st = $db->prepare( 'INSERT INTO nbp_bolnica_pretraga(id_bolnice, id_pretrage) VALUES (:id, :id2)' );

    $st->execute( array( 'id' => '95', 'id2' => '2')); $st->execute( array( 'id' => '102', 'id2' => '3')); $st->execute( array( 'id' => '105', 'id2' => '5')); $st->execute( array( 'id' => '17', 'id2' => '4')); $st->execute( array( 'id' => '110', 'id2' => '1')); $st->execute( array( 'id' => '102', 'id2' => '1')); $st->execute( array( 'id' => '4', 'id2' => '3')); $st->execute( array( 'id' => '55', 'id2' => '3')); $st->execute( array( 'id' => '106', 'id2' => '4')); $st->execute( array( 'id' => '69', 'id2' => '1')); $st->execute( array( 'id' => '37', 'id2' => '1')); $st->execute( array( 'id' => '62', 'id2' => '4')); $st->execute( array( 'id' => '112', 'id2' => '6')); $st->execute( array( 'id' => '7', 'id2' => '5')); $st->execute( array( 'id' => '86', 'id2' => '6')); $st->execute( array( 'id' => '6', 'id2' => '1')); $st->execute( array( 'id' => '109', 'id2' => '4')); $st->execute( array( 'id' => '85', 'id2' => '2')); $st->execute( array( 'id' => '108', 'id2' => '6')); $st->execute( array( 'id' => '93', 'id2' => '6')); $st->execute( array( 'id' => '87', 'id2' => '6')); $st->execute( array( 'id' => '57', 'id2' => '1')); $st->execute( array( 'id' => '100', 'id2' => '6')); $st->execute( array( 'id' => '26', 'id2' => '1')); $st->execute( array( 'id' => '16', 'id2' => '5')); $st->execute( array( 'id' => '77', 'id2' => '4')); $st->execute( array( 'id' => '24', 'id2' => '1')); $st->execute( array( 'id' => '5', 'id2' => '5')); $st->execute( array( 'id' => '91', 'id2' => '2')); $st->execute( array( 'id' => '63', 'id2' => '2')); $st->execute( array( 'id' => '72', 'id2' => '2')); $st->execute( array( 'id' => '102', 'id2' => '6')); $st->execute( array( 'id' => '109', 'id2' => '3')); $st->execute( array( 'id' => '61', 'id2' => '3')); $st->execute( array( 'id' => '91', 'id2' => '5')); $st->execute( array( 'id' => '9', 'id2' => '5')); $st->execute( array( 'id' => '4', 'id2' => '5')); $st->execute( array( 'id' => '76', 'id2' => '3')); $st->execute( array( 'id' => '51', 'id2' => '3')); $st->execute( array( 'id' => '101', 'id2' => '3')); $st->execute( array( 'id' => '6', 'id2' => '3')); $st->execute( array( 'id' => '34', 'id2' => '2')); $st->execute( array( 'id' => '69', 'id2' => '3')); $st->execute( array( 'id' => '21', 'id2' => '5')); $st->execute( array( 'id' => '56', 'id2' => '2')); $st->execute( array( 'id' => '48', 'id2' => '2')); $st->execute( array( 'id' => '72', 'id2' => '4')); $st->execute( array( 'id' => '65', 'id2' => '1')); $st->execute( array( 'id' => '32', 'id2' => '2')); $st->execute( array( 'id' => '55', 'id2' => '1')); $st->execute( array( 'id' => '35', 'id2' => '3')); $st->execute( array( 'id' => '63', 'id2' => '1')); $st->execute( array( 'id' => '82', 'id2' => '1')); $st->execute( array( 'id' => '38', 'id2' => '2')); $st->execute( array( 'id' => '50', 'id2' => '1')); $st->execute( array( 'id' => '63', 'id2' => '3')); $st->execute( array( 'id' => '112', 'id2' => '2')); $st->execute( array( 'id' => '93', 'id2' => '5')); $st->execute( array( 'id' => '17', 'id2' => '6')); $st->execute( array( 'id' => '108', 'id2' => '5')); $st->execute( array( 'id' => '28', 'id2' => '5')); $st->execute( array( 'id' => '82', 'id2' => '3')); $st->execute( array( 'id' => '71', 'id2' => '6')); $st->execute( array( 'id' => '3', 'id2' => '4')); $st->execute( array( 'id' => '51', 'id2' => '6')); $st->execute( array( 'id' => '12', 'id2' => '1')); $st->execute( array( 'id' => '44', 'id2' => '5')); $st->execute( array( 'id' => '68', 'id2' => '5')); $st->execute( array( 'id' => '22', 'id2' => '5')); $st->execute( array( 'id' => '19', 'id2' => '3')); $st->execute( array( 'id' => '28', 'id2' => '4')); $st->execute( array( 'id' => '13', 'id2' => '4')); $st->execute( array( 'id' => '40', 'id2' => '3')); $st->execute( array( 'id' => '66', 'id2' => '4')); $st->execute( array( 'id' => '92', 'id2' => '4')); $st->execute( array( 'id' => '91', 'id2' => '4')); $st->execute( array( 'id' => '87', 'id2' => '1')); $st->execute( array( 'id' => '21', 'id2' => '6')); $st->execute( array( 'id' => '46', 'id2' => '6')); $st->execute( array( 'id' => '81', 'id2' => '3')); $st->execute( array( 'id' => '100', 'id2' => '2')); $st->execute( array( 'id' => '33', 'id2' => '3')); $st->execute( array( 'id' => '1', 'id2' => '5')); $st->execute( array( 'id' => '39', 'id2' => '3')); $st->execute( array( 'id' => '46', 'id2' => '3')); $st->execute( array( 'id' => '59', 'id2' => '1')); $st->execute( array( 'id' => '45', 'id2' => '1')); $st->execute( array( 'id' => '88', 'id2' => '5')); $st->execute( array( 'id' => '18', 'id2' => '2')); $st->execute( array( 'id' => '89', 'id2' => '2')); $st->execute( array( 'id' => '14', 'id2' => '4')); $st->execute( array( 'id' => '59', 'id2' => '2')); $st->execute( array( 'id' => '41', 'id2' => '4')); $st->execute( array( 'id' => '65', 'id2' => '6')); $st->execute( array( 'id' => '17', 'id2' => '2')); $st->execute( array( 'id' => '15', 'id2' => '3')); $st->execute( array( 'id' => '105', 'id2' => '2')); $st->execute( array( 'id' => '54', 'id2' => '1')); $st->execute( array( 'id' => '53', 'id2' => '4')); $st->execute( array( 'id' => '52', 'id2' => '3')); $st->execute( array( 'id' => '112', 'id2' => '1')); $st->execute( array( 'id' => '48', 'id2' => '4')); $st->execute( array( 'id' => '50', 'id2' => '6')); $st->execute( array( 'id' => '8', 'id2' => '6')); $st->execute( array( 'id' => '12', 'id2' => '5')); $st->execute( array( 'id' => '37', 'id2' => '5'));

}
catch( PDOException $e ) { exit( "PDO error kod bolnica_pretraga: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_bolnica_pretraga.<br />";


try
{
    $st = $db->prepare( 'INSERT INTO nbp_termin(oib_pacijenta, id_pretrage, datum, vrijeme, id_bolnice) VALUES (:oib_pacijenta, :id_pretrage, :datum, :vrijeme, :id_bolnice)' );

    $st->execute( array( 'oib_pacijenta' => '10000338099', 'id_pretrage' => '2', 'datum' => '2010-07-02', 'vrijeme' => '12:00', 'id_bolnice' => '100') );
    $st->execute( array( 'oib_pacijenta' => '10000338099', 'id_pretrage' => '1', 'datum' => '2011-07-02', 'vrijeme' => '11:00', 'id_bolnice' => '12') );
    $st->execute( array( 'oib_pacijenta' => '10000917906', 'id_pretrage' => '5', 'datum' => '2012-07-02', 'vrijeme' => '09:00', 'id_bolnice' => '88') );
    $st->execute( array( 'oib_pacijenta' => '10000917906', 'id_pretrage' => '5', 'datum' => '2009-07-02', 'vrijeme' => '15:00', 'id_bolnice' => '88') );
    $st->execute( array( 'oib_pacijenta' => '10000917906', 'id_pretrage' => '3', 'datum' => '2004-07-02', 'vrijeme' => '12:00', 'id_bolnice' => '81') );
    $st->execute( array( 'oib_pacijenta' => '10000395731', 'id_pretrage' => '2', 'datum' => '2005-07-02', 'vrijeme' => '16:00', 'id_bolnice' => '100') );
    $st->execute( array( 'oib_pacijenta' => '10000395731', 'id_pretrage' => '6', 'datum' => '2006-07-02', 'vrijeme' => '11:00', 'id_bolnice' => '50') );
    $st->execute( array( 'oib_pacijenta' => '10000998713', 'id_pretrage' => '2', 'datum' => '2003-07-02', 'vrijeme' => '12:00', 'id_bolnice' => '100') );
    $st->execute( array( 'oib_pacijenta' => '10000520909', 'id_pretrage' => '4', 'datum' => '2007-07-02', 'vrijeme' => '12:00', 'id_bolnice' => '92') );
    $st->execute( array( 'oib_pacijenta' => '10000013006', 'id_pretrage' => '1', 'datum' => '2004-07-02', 'vrijeme' => '13:00', 'id_bolnice' => '12') );
    $st->execute( array( 'oib_pacijenta' => '10000013006', 'id_pretrage' => '1', 'datum' => '2005-07-02', 'vrijeme' => '16:00', 'id_bolnice' => '12') );
    $st->execute( array( 'oib_pacijenta' => '10000013006', 'id_pretrage' => '1', 'datum' => '2015-07-02', 'vrijeme' => '17:40', 'id_bolnice' => '12') );
    $st->execute( array( 'oib_pacijenta' => '10000013006', 'id_pretrage' => '1', 'datum' => '2017-07-02', 'vrijeme' => '12:00', 'id_bolnice' => '87') );
    $st->execute( array( 'oib_pacijenta' => '10000878383', 'id_pretrage' => '6', 'datum' => '2009-07-02', 'vrijeme' => '11:00', 'id_bolnice' => '21') );
    $st->execute( array( 'oib_pacijenta' => '10000402929', 'id_pretrage' => '5', 'datum' => '2018-07-02', 'vrijeme' => '10:00', 'id_bolnice' => '1') );
    $st->execute( array( 'oib_pacijenta' => '10000402929', 'id_pretrage' => '5', 'datum' => '2019-07-02', 'vrijeme' => '12:00', 'id_bolnice' => '1') );

    $st->execute( array( 'oib_pacijenta' => '10000338099', 'id_pretrage' => '5', 'datum' => '2024-07-02', 'vrijeme' => '12:00', 'id_bolnice' => '1') );
    $st->execute( array( 'oib_pacijenta' => '10000917906', 'id_pretrage' => '3', 'datum' => '2024-07-02', 'vrijeme' => '12:00', 'id_bolnice' => '81') );
    $st->execute( array( 'oib_pacijenta' => '10000917906', 'id_pretrage' => '5', 'datum' => '2024-07-05', 'vrijeme' => '12:00', 'id_bolnice' => '12') );
    $st->execute( array( 'oib_pacijenta' => '10000395731', 'id_pretrage' => '1', 'datum' => '2024-07-07', 'vrijeme' => '12:00', 'id_bolnice' => '59') );
    $st->execute( array( 'oib_pacijenta' => '10000998713', 'id_pretrage' => '1', 'datum' => '2024-07-11', 'vrijeme' => '12:00', 'id_bolnice' => '59') );
    $st->execute( array( 'oib_pacijenta' => '10000998713', 'id_pretrage' => '2', 'datum' => '2024-07-02', 'vrijeme' => '12:00', 'id_bolnice' => '100') );
    $st->execute( array( 'oib_pacijenta' => '10000520909', 'id_pretrage' => '6', 'datum' => '2024-07-03', 'vrijeme' => '12:00', 'id_bolnice' => '65') );
    $st->execute( array( 'oib_pacijenta' => '10000013006', 'id_pretrage' => '2', 'datum' => '2024-07-07', 'vrijeme' => '12:00', 'id_bolnice' => '18') );
    $st->execute( array( 'oib_pacijenta' => '10000013006', 'id_pretrage' => '4', 'datum' => '2024-07-04', 'vrijeme' => '12:00', 'id_bolnice' => '48') );
    $st->execute( array( 'oib_pacijenta' => '10000013006', 'id_pretrage' => '4', 'datum' => '2024-07-05', 'vrijeme' => '12:00', 'id_bolnice' => '48') );
    $st->execute( array( 'oib_pacijenta' => '10000878383', 'id_pretrage' => '4', 'datum' => '2024-07-02', 'vrijeme' => '12:00', 'id_bolnice' => '53') );
    $st->execute( array( 'oib_pacijenta' => '10000402929', 'id_pretrage' => '6', 'datum' => '2024-07-03', 'vrijeme' => '12:00', 'id_bolnice' => '8') );

}
catch( PDOException $e ) { exit( "PDO error kod termina: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_termin.<br />";

try
{
    $st = $db->prepare( 'INSERT INTO nbp_mjesto(naziv, gs, gd) VALUES (:naziv, :gs, :gd)' );

    $st->execute( array( 'naziv' => 'Senj', 'gs' => '44.98944', 'gd' => '14.90583') ); $st->execute( array( 'naziv' => 'Split', 'gs' => '43.50891', 'gd' => '16.439154') ); $st->execute( array( 'naziv' => 'Gospić', 'gs' => '44.54611', 'gd' => '15.37472') ); $st->execute( array( 'naziv' => 'Rab', 'gs' => '44.75694', 'gd' => '14.76083') ); $st->execute( array( 'naziv' => 'Cernik', 'gs' => '45.28861', 'gd' => '17.38194') ); $st->execute( array( 'naziv' => 'Novalja', 'gs' => '44.55778', 'gd' => '14.8866') ); $st->execute( array( 'naziv' => 'Popovača', 'gs' => '45.56972', 'gd' => '16.625') ); $st->execute( array( 'naziv' => 'Virovitica', 'gs' => '45.83194', 'gd' => '17.38389') ); $st->execute( array( 'naziv' => 'Varaždinske Toplice', 'gs' => '46.20917', 'gd' => '16.41917') ); $st->execute( array( 'naziv' => 'Vinkovci', 'gs' => '45.28833', 'gd' => '18.80472') ); $st->execute( array( 'naziv' => 'Daruvar', 'gs' => '45.59056', 'gd' => '17.225') ); $st->execute( array( 'naziv' => 'Kutina', 'gs' => '45.475', 'gd' => '16.78194') ); $st->execute( array( 'naziv' => 'Gornja Bistra', 'gs' => '45.91667', 'gd' => '15.9') ); $st->execute( array( 'naziv' => 'Dubrovnik', 'gs' => '42.64125', 'gd' => '18.10909') ); $st->execute( array( 'naziv' => 'Slunj', 'gs' => '45.11456', 'gd' => '15.5843') ); $st->execute( array( 'naziv' => 'Duga Resa', 'gs' => '45.44614', 'gd' => '15.49871') ); $st->execute( array( 'naziv' => 'Knin', 'gs' => '44.04063', 'gd' => '16.19662') ); $st->execute( array( 'naziv' => 'Vela Luka', 'gs' => '42.96333', 'gd' => '16.7225') ); $st->execute( array( 'naziv' => 'Našice', 'gs' => '45.48861', 'gd' => '18.08778') ); $st->execute( array( 'naziv' => 'Krapina', 'gs' => '46.16083', 'gd' => '15.87889') ); $st->execute( array( 'naziv' => 'Lovran', 'gs' => '45.29194', 'gd' => '14.27417') ); $st->execute( array( 'naziv' => 'Korenica', 'gs' => '44.74389', 'gd' => '15.70972') ); $st->execute( array( 'naziv' => 'Zadar', 'gs' => '44.11578', 'gd' => '15.22514') ); $st->execute( array( 'naziv' => 'Bjelovar', 'gs' => '45.89861', 'gd' => '16.84889') ); $st->execute( array( 'naziv' => 'Korčula', 'gs' => '42.96038', 'gd' => '17.13525') ); $st->execute( array( 'naziv' => 'Opatija', 'gs' => '45.33658', 'gd' => '14.30782') ); $st->execute( array( 'naziv' => 'Osijek', 'gs' => '45.55111', 'gd' => '18.69389') ); $st->execute( array( 'naziv' => 'Crikvenica', 'gs' => '45.17722', 'gd' => '14.69278') ); $st->execute( array( 'naziv' => 'Samobor', 'gs' => '45.80306', 'gd' => '15.71806') ); $st->execute( array( 'naziv' => 'Vojnić', 'gs' => '45.32361', 'gd' => '15.69861') ); $st->execute( array( 'naziv' => 'Šibenik', 'gs' => '43.73429', 'gd' => '15.8942') ); $st->execute( array( 'naziv' => 'Stubičke toplice', 'gs' => '45.97585', 'gd' => '15.93238') ); $st->execute( array( 'naziv' => 'Petrinja', 'gs' => '45.4375', 'gd' => '16.29') ); $st->execute( array( 'naziv' => 'Donji Miholjac', 'gs' => '45.76083', 'gd' => '18.16722') ); $st->execute( array( 'naziv' => 'Rijeka', 'gs' => '45.32673', 'gd' => '14.44241') ); $st->execute( array( 'naziv' => 'Beli Manastir', 'gs' => '45.77', 'gd' => '18.60361') ); $st->execute( array( 'naziv' => 'Dražice', 'gs' => '45.39083', 'gd' => '14.47028') ); $st->execute( array( 'naziv' => 'Valpovo', 'gs' => '45.66083', 'gd' => '18.41861') ); $st->execute( array( 'naziv' => 'Zagreb', 'gs' => '45.81444', 'gd' => '15.97798') ); $st->execute( array( 'naziv' => 'Vukovar', 'gs' => '45.35161', 'gd' => '19.00225') ); $st->execute( array( 'naziv' => 'Đakovo', 'gs' => '45.30833', 'gd' => '18.41056') ); $st->execute( array( 'naziv' => 'Rovinj', 'gs' => '45.08268', 'gd' => '13.63457') ); $st->execute( array( 'naziv' => 'Požega', 'gs' => '45.34028', 'gd' => '17.68528') ); $st->execute( array( 'naziv' => 'Metković', 'gs' => '43.05417', 'gd' => '17.64833') ); $st->execute( array( 'naziv' => 'Čakovec', 'gs' => '46.38444', 'gd' => '16.43389') ); $st->execute( array( 'naziv' => 'Ogulin', 'gs' => '45.26611', 'gd' => '15.22861') ); $st->execute( array( 'naziv' => 'Karlovac', 'gs' => '45.49167', 'gd' => '15.55') ); $st->execute( array( 'naziv' => 'Županja', 'gs' => '45.0775', 'gd' => '18.6975') ); $st->execute( array( 'naziv' => 'Ivanić Grad', 'gs' => '45.70833', 'gd' => '16.39694') ); $st->execute( array( 'naziv' => 'Nova Gradiška', 'gs' => '45.255', 'gd' => '17.38306') ); $st->execute( array( 'naziv' => 'Velika Gorica', 'gs' => '45.7125', 'gd' => '16.07556') ); $st->execute( array( 'naziv' => 'Slavonski Brod', 'gs' => '45.16028', 'gd' => '18.01556') ); $st->execute( array( 'naziv' => 'Otočac', 'gs' => '44.86944', 'gd' => '15.2375') ); $st->execute( array( 'naziv' => 'Drniš', 'gs' => '43.8625', 'gd' => '16.15556') ); $st->execute( array( 'naziv' => 'Makarska', 'gs' => '43.29694', 'gd' => '17.01778') ); $st->execute( array( 'naziv' => 'Sisak', 'gs' => '45.46611', 'gd' => '16.37833') ); $st->execute( array( 'naziv' => 'Ozalj', 'gs' => '45.61293', 'gd' => '15.47771') ); $st->execute( array( 'naziv' => 'Zabok', 'gs' => '46.02626', 'gd' => '15.90391') ); $st->execute( array( 'naziv' => 'Varaždin', 'gs' => '46.30444', 'gd' => '16.33778') ); $st->execute( array( 'naziv' => 'Lipik', 'gs' => '45.41139', 'gd' => '17.15222') ); $st->execute( array( 'naziv' => 'Ploče', 'gs' => '43.05611', 'gd' => '17.43278') ); $st->execute( array( 'naziv' => 'Ugljan', 'gs' => '44.13083', 'gd' => '15.10306') ); $st->execute( array( 'naziv' => 'Krapinske Toplice', 'gs' => '46.09333', 'gd' => '15.84333') ); $st->execute( array( 'naziv' => 'Pula', 'gs' => '44.86833', 'gd' => '13.84806') ); $st->execute( array( 'naziv' => 'Biograd na Moru', 'gs' => '43.94333', 'gd' => '15.45194') ); $st->execute( array( 'naziv' => 'Koprivnica', 'gs' => '46.16278', 'gd' => '16.8275') );
}
catch( PDOException $e ) { exit( "PDO error kod mjesta: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_mjesto.<br />";

//---------------------------------------------------------------------
//--funkcije

//-- zracna udaljenost gradova

try
{
    $st = $db->prepare(
      'CREATE FUNCTION udaljenost(mjesto1 character varying(25), mjesto2 character varying(25))
          RETURNS numeric
      AS $$
      DECLARE
          v_gs1 numeric;
          v_gs2 numeric;
          v_gd1 numeric;
          v_gd2 numeric;
          v_pom numeric;
      BEGIN
          SELECT gs, gd INTO v_gs1, v_gd1
              FROM nbp_mjesto
                  WHERE naziv = mjesto1;

          SELECT gs, gd INTO v_gs2, v_gd2
              FROM nbp_mjesto
                  WHERE naziv = mjesto2;
          v_pom = SIN(RADIANS(v_gs1)) * SIN(RADIANS(v_gs2)) + COS(RADIANS(v_gs1)) * COS(RADIANS(v_gs2)) * COS(RADIANS(v_gd2) - RADIANS(v_gd1));

          RETURN acos(v_pom) * 6371;
      END;
      $$ LANGUAGE plpgsql;');
    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za udaljenost: " . $e->getMessage() ); }

echo "Napravio funkciju udaljenost.<br>";

//-- punjenje tablice susjedi
try
{
    $st = $db->prepare(
      'CREATE FUNCTION punjenje_susjedi()
          RETURNS text
      AS $$
      DECLARE
          v_bolnica1 RECORD;
          v_bolnica2 RECORD;
          i INT;
          n INT;
      BEGIN
          SELECT count(*) INTO n
              FROM nbp_bolnica;

          i = 0;
          FOR v_bolnica1 IN
              SELECT *
                  FROM nbp_bolnica
              LIMIT(n-1)
          LOOP
              i = i + 1;
              FOR v_bolnica2 IN
                  SELECT *
                      FROM nbp_bolnica
                  ORDER BY id DESC
                  LIMIT(n-i)
              LOOP
                  IF (udaljenost(v_bolnica1.mjesto, v_bolnica2.mjesto) <= 75.00)
                      THEN
                          INSERT INTO nbp_susjedi VALUES
                              (v_bolnica1.id, v_bolnica2.id);
                  END IF;
              END LOOP;
          END LOOP;

          RETURN \'Ispunjena tablica SUSJEDI.\';
      END;
      $$ LANGUAGE plpgsql;');
    $st->execute();

    $st2=$db->prepare('SELECT * FROM punjenje_susjedi();');
    $st2->execute();
}
catch( PDOException $e ) { exit( "PDO error za punjenje_susjedi: " . $e->getMessage() ); }

echo "Napravio funkciju punjenje_susjedi.<br>";


//-- povijest pretraga
try
{
    $st = $db->prepare(
      'CREATE FUNCTION povijest_pretraga(oib CHAR(11))
            RETURNS table (
                datum DATE,
                -- vrijeme vjerojatno nebitno, al ako zatreba, mozemo staviti
                vrsta CHAR VARYING (20),
                ime_bolnice  CHAR VARYING (30) -- kod ispisa u aplikaciji, umjesto ID_bolnice ispisati ime bolnice koje se dohvati iz grafovske baze
            )
        AS $$
        BEGIN
            RETURN QUERY
                SELECT nbp_termin.datum, nbp_pretraga.vrsta, nbp_bolnica.ime
                    FROM nbp_termin
                        LEFT JOIN nbp_bolnica
                            ON id = id_bolnice
                        LEFT JOIN nbp_pretraga
                            ON nbp_termin.id_pretrage = nbp_pretraga.id
                WHERE oib_pacijenta = oib
                ORDER BY datum DESC; -- najnovije pretrage prve
        END;
        $$ LANGUAGE plpgsql;');
    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za povijest_pretraga: " . $e->getMessage() ); }

echo "Napravio funkciju povijest_pretraga.<br>";



//-- popis pacijenata
try
{
    $st = $db->prepare(
      'CREATE FUNCTION popis_pacijenata(oib_L CHAR(11))
            RETURNS table (
                prezime CHAR VARYING(20),
                ime CHAR VARYING(20),
                oib CHAR(11),
                mbo CHAR(9)
                -- ostale podatke moze dobiti kad klikne na pojedinog pacijenta u aplikaciji
            )
        AS $$
        BEGIN
            RETURN QUERY
                SELECT prezime, ime, oib, mbo
                    FROM nbp_pacijent
                WHERE oib_lijecnika = oib_L
            ORDER BY prezime, ime;
        END;
        $$ LANGUAGE plpgsql;');
    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za popis_pacijenata: " . $e->getMessage() ); }

echo "Napravio funkciju popis_pacijenata.<br>";

try
{
    $st = $db->prepare(
      'CREATE FUNCTION lista_cekanja(ime_bolnice CHAR VARYING(30), vrsta_P CHAR VARYING(20))
            RETURNS table (
                datum DATE,
                vrijeme TIME,
                oib_pacijenta CHAR(11)
                )
        AS $$
        DECLARE
            v_id_bolnice    INT;
            v_id_pretrage   INT;
        BEGIN
        SELECT id INTO v_id_bolnice
            FROM nbp_bolnica
            WHERE ime = ime_bolnice;

        SELECT id INTO v_id_pretrage
            FROM nbp_pretraga
            WHERE vrsta = vrsta_P;

        RETURN QUERY
            SELECT datum, vrijeme, oib_pacijenta
                FROM nbp_termin
            WHERE datum >= curdate()
            AND vrijeme > curtime()
            AND id_bolnice = v_id_bolnice
            AND id_pretrage = v_id_pretrage
        ORDER BY datum, vrijeme;
        END;
        $$ LANGUAGE plpgsql;');
    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za lista_cekanja: " . $e->getMessage() ); }

echo "Napravio funkciju lista_cekanja.<br>";

try
{
    $st = $db->prepare(
      'CREATE FUNCTION prvi_termin(ime_bolnice CHAR VARYING(30), vrsta_P CHAR VARYING(20))
            RETURNS table (
                datum DATE,
                vrijeme TIME
                )
        AS $$
        DECLARE
            v_id_bolnice    INT;
            v_id_pretrage   INT;
            v_trajanje      INT;
            v_datum         DATE;
            v_vrijeme       TIME;
        BEGIN
            SELECT id INTO v_id_bolnice
                FROM nbp_bolnica
                WHERE ime = ime_bolnice;

            SELECT id, trajanje_min INTO v_id_pretrage, v_trajanje
                FROM nbp_pretraga
                WHERE vrsta = vrsta_P;

            SELECT datum, vrijeme INTO v_datum, v_vrijeme
                FROM nbp_termin
                WHERE id_pretrage = v_id_pretrage
                AND id_bolnice = v_id_bolnice
                ORDER BY datum, vrijeme DESC
                LIMIT 1;

            IF (v_vrijeme + v_trajanje * INTERVAL \'1 minute\') <= \'18:00\'::TIME
                THEN
                    RETURN QUERY
                        SELECT v_datum AS datum, v_vrijeme + v_trajanje * INTERVAL \'1 minute\' AS vrijeme;
            ELSE
                RETURN QUERY
                    SELECT v_datum + INTERVAL \'1 day\' AS datum, \'7:00\'::TIME AS vrijeme;
            END IF;
        END;
        $$ LANGUAGE plpgsql;');
    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za prvi_termin: " . $e->getMessage() ); }

echo "Napravio funkciju prvi_termin.<br>";

?>
