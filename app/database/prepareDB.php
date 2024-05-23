<?php

require_once 'db.class.php';

$db = DB::getConnection();

try
{
    $st = $db->prepare(
        'CREATE TABLE IF NOT EXISTS nbp_lijecnik(
          oib char(11) check (not null),
          ime character varying(20) check (not null),
          prezime character varying(20) check (not null),
          datum_rodjenja date check (not null),
          adresa_ambulante character varying(30) check (not null),
          mjesto_ambulante character varying(20) check (not null),
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
          oib char(11) check (not null),
          mbo char(9), --maticni broj osiguranika, ako pacijent ima zdravstveno
          ime character varying(20) check (not null),
          prezime character varying(20) check (not null),
          datum_rodjenja date check (not null),
          adresa character varying(30) check (not null), -- trenutna adresa boravista
          mjesto character varying(20) check (not null), -- trenutno mjesto boravista -> na temelju toga racunamo udaljenost od bolnica
          oib_lijecnika char(11) check (not null), -- oib lijecnika opce prakse
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
        'CREATE TABLE IF NOT EXISTS nbp_pretraga(
          oib_pacijenta char(11) check (not null),
          vrsta char varying (20) check (not null),
          datum date check (not null),
          vrijeme time check (not null),
          id_bolnice int check (not null), -- ovo je referenca na PK iz grafovske baze. to ne treba nikak referencirati valjda onda
          constraint pkPretraga primary key (oib_pacijenta, datum, vrijeme),
          constraint fkPretraga foreign key (oib_pacijenta) references nbp_pacijent(oib)
        );'
    );

    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za nbp_pretraga: " . $e->getMessage() ); }

echo "Napravio tablicu nbp_pretraga.<br>";


try
{
    $st = $db->prepare('CREATE INDEX IF NOT EXISTS pacijent_ime_idx ON nbp_pacijent(prezime, ime);');
    $st->execute();

    $st = $db->prepare('CREATE INDEX IF NOT EXISTS pretraga_pacijent_idx ON nbp_pretraga(oib_pacijenta);');
    $st->execute();

    $st = $db->prepare('CREATE INDEX IF NOT EXISTS pretraga_lista_cekanja_idx ON nbp_pretraga(id_bolnice, vrsta);');
    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za indexe: " . $e->getMessage() ); }

echo "Napravio indexe.<br>";

try
{
    $st = $db->prepare(
      'CREATE FUNCTION povijest_pretraga(oib CHAR(11))
            RETURNS table (
                datum DATE,
                -- vrijeme vjerojatno nebitno, al ako zatreba, mozemo staviti
                vrsta CHAR VARYING (20),
                id_bolnice INT -- kod ispisa u aplikaciji, umjesto ID_bolnice ispisati ime bolnice koje se dohvati iz grafovske baze
            )
        AS $$
        BEGIN
            RETURN QUERY
                SELECT datum, vrsta, id_bolnice
                    FROM nbp_pretraga
                WHERE oib_pacijenta = oib
                ORDER BY datum;
        END;
        $$ LANGUAGE plpgsql;');
    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za povijest_pretraga: " . $e->getMessage() ); }

echo "Napravio funkciju povijest_pretraga.<br>";

try
{
    $st = $db->prepare(
      'CREATE FUNCTION popis_pacijenata(oib_L CHAR(11))
            RETURNS table (
                prezime CHAR VARYING(20),
                ime CHAR VARYING(20),
                oib CHAR(11),
                mbo CHAR(9)
                -- eventualno mozemo dodati i ostale podatke, ali to je mozda vise za aplikaciju kad klikne na pojedinog pacijenta
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
      'CREATE FUNCTION lista_cekanja(bolnica INT, vrsta_P CHAR VARYING(20))
            RETURNS table (
                datum DATE,
                vrijeme TIME,
                oib_pacijenta CHAR(11)
                )
        AS $$
        BEGIN
            RETURN QUERY
                SELECT datum, vrijeme, oib_pacijenta
                    FROM nbp_pretraga
                WHERE datum >= date(now())
                  AND vrijeme > time(now())
                  AND id_bolnice = bolnica
                  AND vrsta = vrsta_P
            ORDER BY datum, vrijeme;
        END;
        $$ LANGUAGE plpgsql;');
    $st->execute();
}
catch( PDOException $e ) { exit( "PDO error za lista_cekanja: " . $e->getMessage() ); }

echo "Napravio funkciju lista_cekanja.<br>";

try
{
    $st = $db->prepare( 'INSERT INTO nbp_lijecnik(oib,ime,prezime,datum_rodjenja,adresa_ambulante,mjesto_ambulante) VALUES (:oib,:ime,:prezime,:datum_rodjenja,:adresa_ambulante,:mjesto_ambulante)');

    $st->execute( array( 'oib' => '10000444444', 'ime' => 'Gandalf', 'prezime' => 'Mithrandir', 'datum_rodjenja' => '1940-07-02', 'adresa_ambulante' => 'Glavna 8', 'mjesto_ambulante' => 'Bree') );
    $st->execute( array( 'oib' => '10000291105', 'ime' => 'Aragorn', 'prezime' => 'Elessar', 'datum_rodjenja' => '1980-07-02', 'adresa_ambulante' => 'Glavna 1', 'mjesto_ambulante' => 'Minas Tirith') );
    $st->execute( array( 'oib' => '10000062905', 'ime' => 'Legolas', 'prezime' => 'Thranduilion', 'datum_rodjenja' => '1970-07-02', 'adresa_ambulante' => 'Glavna 7', 'mjesto_ambulante' => 'Mirkwood') );
    $st->execute( array( 'oib' => '10000999919', 'ime' => 'Theoden', 'prezime' => 'Ednew', 'datum_rodjenja' => '1960-07-02', 'adresa_ambulante' => 'Glavna 5', 'mjesto_ambulante' => 'Bree') );
    $st->execute( array( 'oib' => '10000857999', 'ime' => 'Arwen', 'prezime' => 'Undomiel', 'datum_rodjenja' => '1970-07-02', 'adresa_ambulante' => 'Glavna 4', 'mjesto_ambulante' => 'Rivendell') );
    $st->execute( array( 'oib' => '10000893743', 'ime' => 'Denethor', 'prezime' => 'Ecthelion', 'datum_rodjenja' => '1950-07-02', 'adresa_ambulante' => 'Glavna 9', 'mjesto_ambulante' => 'Minas Tirith') );
    $st->execute( array( 'oib' => '10000891233', 'ime' => 'Tom', 'prezime' => 'Bombadil', 'datum_rodjenja' =>'1940-07-02', 'adresa_ambulante' => 'Glavna 2', 'mjesto_ambulante' => 'Bree') );
    $st->execute( array( 'oib' => '10000213905', 'ime' => 'Elendil', 'prezime' => 'Voronda', 'datum_rodjenja' => '1960-07-02', 'adresa_ambulante' => 'Glavna 11', 'mjesto_ambulante' => 'Numenor') );
    $st->execute( array( 'oib' => '10000294736', 'ime' => 'Feanor', 'prezime' => 'Curufinwe', 'datum_rodjenja' => '1960-07-02', 'adresa_ambulante' => 'Glavna 12', 'mjesto_ambulante' => 'Tirion') );
    $st->execute( array( 'oib' => '10000243905', 'ime' => 'Maedhros', 'prezime' => 'Nelyafinwe', 'datum_rodjenja' => '1980-07-02', 'adresa_ambulante' => 'Glavna 6', 'mjesto_ambulante' => 'Tirion') );
    $st->execute( array( 'oib' => '10000432043', 'ime' => 'Finwe', 'prezime' => 'Noldoran', 'datum_rodjenja' => '1940-07-02', 'adresa_ambulante' => 'Glavna 13', 'mjesto_ambulante' => 'Tirion') );
    $st->execute( array( 'oib' => '10000794735', 'ime' => 'Elrond', 'prezime' => 'Peredhel', 'datum_rodjenja' => '1940-07-02', 'adresa_ambulante' => 'Glavna 3', 'mjesto_ambulante' => 'Rivendell') );

}
catch( PDOException $e ) { exit( "PDO error kod lijecnika: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_lijecnik.<br />";

try
{
    $st = $db->prepare( 'INSERT INTO nbp_pacijent(oib, mbo, ime, prezime, datum_rodjenja, adresa, mjesto, oib_lijecnika) VALUES (:oib, :mbo, :ime, :prezime, :datum_rodjenja, :adresa, :mjesto, :oib_lijecnika)' );

    $st->execute( array( 'oib' => '10000338099', 'mbo' => '100338099', 'ime' => 'Frodo', 'prezime' => 'Baggins', 'datum_rodjenja' => '2000-07-02', 'adresa' => 'Glavna 1', 'mjesto' => 'Hobbiton', 'oib_lijecnika' => '10000444444') );
    $st->execute( array( 'oib' => '10000917906', 'mbo' => '100917906', 'ime' => 'Samwise', 'prezime' => 'Gamgee', 'datum_rodjenja' => '1999-07-02', 'adresa' => 'Glavna 3', 'mjesto' => 'Hobbiton', 'oib_lijecnika' => '10000891233') );
    $st->execute( array( 'oib' => '10000998713', 'mbo' => '100998713', 'ime' => 'Meriadoc', 'prezime' => 'Brandybuck', 'datum_rodjenja' => '1998-07-02', 'adresa' => 'Glavna 2', 'mjesto' => 'Buckland', 'oib_lijecnika' => '10000891233') );
    $st->execute( array( 'oib' => '10000395731', 'mbo' => '100395731', 'ime' => 'Peregrin', 'prezime' => 'Took', 'datum_rodjenja' => '2001-07-02', 'adresa' => 'Glavna 4', 'mjesto' => 'Buckland', 'oib_lijecnika' => '10000444444') );
    $st->execute( array( 'oib' => '10000520909', 'mbo' => '100520909', 'ime' => 'Boromir', 'prezime' => 'Echtelion', 'datum_rodjenja' => '1980-07-02', 'adresa' => 'Glavna 5', 'mjesto' => 'Minas Tirith', 'oib_lijecnika' => '10000291105') );
    $st->execute( array( 'oib' => '10000013006', 'mbo' => '100013006', 'ime' => 'Faramir', 'prezime' => 'Echtelion', 'datum_rodjenja' => '1990-07-02', 'adresa' => 'Glavna 5', 'mjesto' => 'Minas Tirith', 'oib_lijecnika' => '10000893743') );
    $st->execute( array( 'oib' => '10000878383', 'mbo' => '100878383', 'ime' => 'Eowyn', 'prezime' => 'Eadig', 'datum_rodjenja' => '1990-07-02', 'adresa' => 'Glavna 7', 'mjesto' => 'Edoras', 'oib_lijecnika' => '10000999919') );
    $st->execute( array( 'oib' => '10000402929', 'mbo' => '100402929', 'ime' => 'Eomer', 'prezime' => 'Eadig', 'datum_rodjenja' => '1990-07-02', 'adresa' => 'Glavna 7', 'mjesto' => 'Edoras', 'oib_lijecnika' => '10000999919') );

}
catch( PDOException $e ) { exit( "PDO error kod pacijenata: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_pacijent.<br />";

try
{
    $st = $db->prepare( 'INSERT INTO nbp_pretraga(oib_pacijenta, vrsta, datum, vrijeme, id_bolnice) VALUES (:oib_pacijenta, :vrsta, :datum, :vrijeme, :id_bolnice)' );

    $st->execute( array( 'oib' => '10000338099', 'vrsta' => 'dijabetes', 'datum' => '2024-07-02', 'vrijeme' => '14:00', 'id_bolnice' => '1') );
    $st->execute( array( 'oib' => '10000917906', 'vrsta' => 'bakteriologija', 'datum' => '2024-02-11', 'vrijeme' => '14:00', 'id_bolnice' => '1') );
    $st->execute( array( 'oib' => '10000395731', 'vrsta' => 'serologija', 'datum' => '2024-07-24', 'vrijeme' => '14:00', 'id_bolnice' => '2') );
    $st->execute( array( 'oib' => '10000013006', 'vrsta' => 'genetika', 'datum' => '2024-07-05', 'vrijeme' => '14:00', 'id_bolnice' => '3') );
    $st->execute( array( 'oib' => '10000402929', 'vrsta' => 'dermatologija', 'datum' => '2024-02-12', 'vrijeme' => '14:00', 'id_bolnice' => '5') );

}
catch( PDOException $e ) { exit( "PDO error kod pretraga: " . $e->getMessage() ); }

echo "Ubacio u tablicu nbp_pretraga.<br />";

?>
