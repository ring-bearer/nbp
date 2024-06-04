<?php

require_once 'db.class.php';

$db = DB::getConnection();

try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_bolnica(
            id serial check (not null),
            ime character varying(30) check (not null),
            adresa character varying(30) check (not null),
            mjesto character varying(20) check (not null),
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
        'CREATE TABLE IF NOT EXISTS nbp_pretraga(
            id int check (not null),
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
            constraint fkPacijent foreign key (oib_lijecnika) references nbp_lijecnik(oib)
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
            constraint fkPacijent foreign key (oib_pacijenta) references nbp_pacijent(oib),
            constraint fkPretraga foreign key (id_pretrage) references nbp_pretraga(id),
            constraint fkBolnica foreign key (id_bolnice) references nbp_bolnica(id)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_termin: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_termin.<br>";

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


//---------------------------------------------------------------------
//--funkcije

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
                SELECT datum, vrsta, ime
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
    $st = $db->prepare('CREATE FUNCTION blaermin(ime_bolnice CHAR VARYING(30), vrsta_P CHAR VARYING(20))
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
        v_kon_vrijeme   TIME;
      BEGIN
      SELECT id INTO v_id_bolnice
          FROM nbp_bolnica
          WHERE ime = ime_bolnice;

      SELECT id,trajanje_min INTO v_id_pretrage,v_trajanje
          FROM nbp_pretraga
          WHERE vrsta = vrsta_P;

          SELECT datum, vrijeme INTO v_datum,v_vrijeme
              FROM nbp_termin
              WHERE id_pretrage = v_id_pretrage
              AND id_bolnice = v_id_bolnice
              ORDER BY datum, vrijeme DESC
              LIMIT 1;

      SELECT v_datum AS datum, v_kon_vrijeme AS vrijeme;
      v_kon_vrijeme=v_vrijeme+(v_trajanje);

          RETURN QUERY
            SELECT v_datum AS datum, v_kon_vrijeme AS vrijeme;
      END;
      $$ LANGUAGE plpgsql;'
      );
    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za prvi_termin: " . $e->getMessage() ); }

echo "Napravio funkciju prvi_termin.<br>";

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

// Vise nije ovakva pretraga
/*try
{
    $st = $db->prepare( 'INSERT INTO nbp_pretraga(oib_pacijenta, vrsta, datum, vrijeme, id_bolnice) VALUES (:oib_pacijenta, :vrsta, :datum, :vrijeme, :id_bolnice)' );

    $st->execute( array( 'oib_pacijenta' => '10000338099', 'vrsta' => 'dijabetes', 'datum' => '2024-07-02', 'vrijeme' => '14:00', 'id_bolnice' => '1') );
    $st->execute( array( 'oib_pacijenta' => '10000917906', 'vrsta' => 'bakteriologija', 'datum' => '2024-02-11', 'vrijeme' => '14:00', 'id_bolnice' => '1') );
    $st->execute( array( 'oib_pacijenta' => '10000395731', 'vrsta' => 'serologija', 'datum' => '2024-07-24', 'vrijeme' => '14:00', 'id_bolnice' => '2') );
    $st->execute( array( 'oib_pacijenta' => '10000013006', 'vrsta' => 'genetika', 'datum' => '2024-07-05', 'vrijeme' => '14:00', 'id_bolnice' => '3') );
    $st->execute( array( 'oib_pacijenta' => '10000402929', 'vrsta' => 'dermatologija', 'datum' => '2024-02-12', 'vrijeme' => '14:00', 'id_bolnice' => '5') );

}
catch( PDOException $e ) { exit( "PDO error kod pretraga: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_pretraga.<br />";*/

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

    $st->execute( array( 'ime' => 'Citadel', 'adresa' => 'Glavna 1', 'mjesto' => 'Minas Tirith') );
    $st->execute( array( 'ime' => 'Imladris', 'adresa' => 'Glavna 1', 'mjesto' => 'Rivendell' ));
    $st->execute( array( 'ime' => 'Meduseld', 'adresa' => 'Glavna 1', 'mjesto' => 'Edoras' ));
    $st->execute( array( 'ime' => 'Rhovanion', 'adresa' => 'Glavna 1', 'mjesto' => 'Mirkwood') );
    $st->execute( array( 'ime' => 'Prancing Pony', 'adresa' => 'Glavna 1', 'mjesto' => 'Bree' ));
    $st->execute( array( 'ime' => 'Armenelos', 'adresa' => 'Glavna 1', 'mjesto' => 'Numenor' ));
    $st->execute( array( 'ime' => 'Valinor', 'adresa' => 'Glavna 1', 'mjesto' => 'Tirion' ));
}
catch( PDOException $e ) { exit( "PDO error kod bolnica: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_bolnica.<br />";

?>
