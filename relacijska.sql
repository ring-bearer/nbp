-- tablice

create table nbp_bolnica(
    id int check (not null),
    ime character varying(30) check (not null),
    adresa character varying(30) check (not null),
    mjesto character varying(20) check (not null),
    constraint pkBolnica primary key (id)
);

create table nbp_pretraga(
    id int check (not null),
    vrsta character varying(30) check (not null),
    trajanje_min int check (not null), -- ukljucuje ciscenje nakon pretrage i kratku pauzu ako je potrebno
    constraint pkPretraga primary key (id)
);

create table nbp_bolnica_pretraga(
    id_bolnice int check (not null),
    id_pretrage int check (not null),
    constraint pkBP primary key (id_bolnice, id_pretrage),
    constraint fkBolnica foreign key (id_bolnice) references nbp_bolnica(id),
    constraint fkPretraga foreign key (id_pretrage) references nbp_pretraga(id)
);

create table nbp_susjedi( -- bolnice unutar 100 km jedna od druge
    id_bolnice1 int check (not null),
    id_bolnice2 int check (not null),
    constraint pkSusjedi primary key (id_bolnice1, id_bolnice2),
    constraint fkBolnica1 foreign key (id_bolnice1) references nbp_bolnica(id),
    constraint fkBolnica2 foreign key (id_bolnice2) references nbp_bolnica(id)
);

create table nbp_admin(
    oib char(11) not null check (oib ~ '^[0-9]{11}$'),
    ime character varying(20) check (not null),
    prezime character varying(20) check (not null),
    lozinka char(10) check (not null), -- lozinka za prijavu u sustav
    constraint pk_Admin primary key (oib)
);

-- lijecnik opce prakse, ne specijalist
create table nbp_lijecnik(
    oib char(11) not null check (oib ~ '^[0-9]{11}$'),
    ime character varying(20) check (not null),
    prezime character varying(20) check (not null),
    datum_rodjenja date check (not null),
    adresa_ambulante character varying(30) check (not null),
    mjesto_ambulante character varying(20) check (not null),
    lozinka character varying(10) check (not null), -- lozinka za prijavu u sustav
    constraint pkLijecnik primary key (oib)
);

create table nbp_pacijent(
    oib char(11) not null check (oib ~ '^[0-9]{11}$'),
    mbo char(9), --maticni broj osiguranika, ako pacijent ima zdravstveno
    ime character varying(20) check (not null),
    prezime character varying(20) check (not null),
    datum_rodjenja date check (not null),
    adresa character varying(30) check (not null), -- trenutna adresa boravista
    mjesto character varying(20) check (not null), -- trenutno mjesto boravista -> na temelju toga racunamo udaljenost od bolnica
    oib_lijecnika char(11) not null check (oib_lijecnika ~ '^[0-9]{11}$'), -- oib lijecnika opce prakse
    lozinka character varying(10) check (not null), -- lozinka za prijavu u sustav
    constraint pkPacijent primary key (oib),
    constraint fkPacijent foreign key (oib_lijecnika) references nbp_lijecnik(oib)
);


create table nbp_termin(
    oib_pacijenta char(11) not null,
    id_pretrage int check (not null),
    datum date check (not null),
    vrijeme time check (not null),
    id_bolnice int check (not null),
    constraint pkTermin primary key (oib_pacijenta, datum, vrijeme),
    constraint fkPacijent foreign key (oib_pacijenta) references nbp_pacijent(oib),
    constraint fkPretraga foreign key (id_pretrage) references nbp_pretraga(id),
    constraint fkBolnica foreign key (id_bolnice) references nbp_bolnica(id)
);


---------------------------------------------------------------------

-- indeksi za ubrzavanje upita

CREATE INDEX pacijent_ime_idx ON nbp_pacijent(prezime, ime);
CREATE INDEX termin_pacijent_idx ON nbp_termin(oib_pacijenta);

---------------------------------------------------------------------
--funkcije

-- povijest pretraga
CREATE FUNCTION povijest_pretraga(oib CHAR(11))
    RETURNS table (
        datum DATE,
        vrsta CHAR VARYING (20),
        ime_bolnice  CHAR VARYING (30)
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
$$ LANGUAGE plpgsql;

-- popis pacijenata
CREATE FUNCTION popis_pacijenata(oib_L CHAR(11))
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
$$ LANGUAGE plpgsql;


-- lista cekanja
CREATE FUNCTION lista_cekanja(ime_bolnice CHAR VARYING(30), vrsta_P CHAR VARYING(20))
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
        WHERE datum >= date(now())
          AND vrijeme > time(now())
          AND id_bolnice = v_id_bolnice
          AND id_pretrage = v_id_pretrage
    ORDER BY datum, vrijeme;
END;
$$ LANGUAGE plpgsql;

-- jos treba jednu za slobodne termine, al to opet treba povlaciti iz grafovske baze
-- prvi slobodan termin
CREATE FUNCTION prvi_termin (ime_bolnice CHAR VARYING(30), vrsta_P CHAR VARYING(20))
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

    IF v_vrijeme + v_trajanje * INTERVAL '1 minute' <= '18:00'::TIME
        THEN
            RETURN QUERY
                SELECT v_datum AS datum, v_vrijeme + v_trajanje * INTERVAL '1 minute' AS vrijeme;
    ELSE
        RETURN QUERY
            SELECT v_datum + INTERVAL '1 day' AS datum, '7:00'::TIME AS vrijeme;
    END IF;
END; -- baca neku gresku na ovom tocka-zarezu???
$$ LANGUAGE plpgsql;

---------------------------------------------------------------------

-- ubacivanje testnih podataka

insert into nbp_lijecnik values
    (10000444444, 'Gandalf', 'Mithrandir', '1940-07-02', 'Glavna 8', 'Bree', 1234567890),
    (10000291105, 'Aragorn', 'Elessar', '1980-07-02', 'Glavna 1', 'Minas Tirith', 1234567890),
    (10000062905, 'Legolas', 'Thranduilion', '1970-07-02', 'Glavna 7', 'Mirkwood', 1234567890),
    (10000999919, 'Theoden', 'Ednew', '1960-07-02', 'Glavna 5', 'Edoras', 1234567890),
    (10000857999, 'Arwen', 'Undomiel', '1970-07-02', 'Glavna 4', 'Rivendell', 1234567890),
    (10000893743, 'Denethor', 'Ecthelion', '1950-07-02', 'Glavna 9', 'Minas Tirith', 1234567890),
    (10000891233, 'Tom', 'Bombadil', '1940-07-02', 'Glavna 2', 'Bree', 1234567890),
    (10000213905, 'Elendil', 'Voronda', '1960-07-02', 'Glavna 11', 'Numenor', 1234567890),
    (10000294736, 'Feanor', 'Curufinwe', '1960-07-02', 'Glavna 12', 'Tirion', 1234567890),
    (10000243905, 'Maedhros', 'Nelyafinwe', '1980-07-02', 'Glavna 6', 'Tirion', 1234567890),
    (10000432043, 'Finwe', 'Noldoran', '1940-07-02', 'Glavna 13', 'Tirion', 1234567890),
    (10000794735, 'Elrond', 'Peredhel', '1940-07-02', 'Glavna 3', 'Rivendell', 1234567890);

select * from nbp_lijecnik;

insert into nbp_pacijent values
    (10000338099, 100338099, 'Frodo', 'Baggins', '2000-07-02', 'Glavna 1', 'Hobbiton', 10000444444, 0123456789),
    (10000917906, 100917906, 'Samwise', 'Gamgee', '1999-07-02', 'Glavna 3', 'Hobbiton', 10000891233, 0123456789),
    (10000998713, 100998713, 'Meriadoc', 'Brandybuck', '1998-07-02', 'Glavna 2', 'Buckland', 10000891233, 0123456789),
    (10000395731, 100395731, 'Peregrin', 'Took', '2002-07-02', 'Glavna 4', 'Buckland', 10000444444, 0123456789),
    (10000520909, 100520909, 'Boromir', 'Echtelion', '1980-07-02', 'Glavna 5', 'Minas Tirith', 10000291105, 0123456789),
    (10000013006, 100013006, 'Faramir', 'Echtelion', '1990-07-02', 'Glavna 5', 'Minas Tirith', 10000893743, 0123456789),
    (10000878383, 100878383, 'Eowyn', 'Eadig', '1990-07-02', 'Glavna 7', 'Edoras', 10000999919, 0123456789),
    (10000402929, 100402929, 'Eomer', 'Eadig', '1990-07-02', 'Glavna 7', 'Edoras', 10000999919, 0123456789);

select * from nbp_pacijent;


insert into nbp_termin values
    (10000338099, 'dijabetes', '2024-07-02', '14:00', 1),
    (10000917906, 'bakteriologija', '2024-02-11', '14:00', 1),
    (10000395731, 'serologija', '2024-07-24', '14:00', 2),
    (10000013006, 'genetika', '2024-07-05', '14:00', 3),
    (10000402929, 'dermatologija', '2024-02-12', '14:00', 5);

select * from nbp_termin;
