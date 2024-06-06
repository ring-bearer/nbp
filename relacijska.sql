-- trigger da se u nbp_termin ubaci samo pretraga koju odredena bolnica nudi

-- tablice

create table nbp_bolnica(
    id serial check (not null),
    ime character varying(100) check (not null),
    adresa character varying(50) check (not null),
    mjesto character varying(20) check (not null),
    constraint pkBolnica primary key (id)
);

create table nbp_mjesto(
    naziv character varying(25) check (not null),
    gs numeric check (not null), -- znamo da je N
    gd numeric check (not null), -- znamo da je E
    constraint pkMjesto primary key (naziv)
);

create table nbp_pretraga(
    id serial check (not null),
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

-- !!! paziti da ispise i bolnicu kojoj pacijent pripada - nje nema u tablici
create table nbp_susjedi( -- bolnice unutar 75 km zracne udaljenosti jedna od druge
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
    password_hash character varying(20) check (not null), -- lozinka za prijavu u sustav
    constraint pk_Admin primary key (oib)
);

-- lijecnik opce prakse, ne specijalist
create table nbp_lijecnik(
    oib char(11) not null check (oib ~ '^[0-9]{11}$'),
    ime character varying(20) check (not null),
    prezime character varying(20) check (not null),
    datum_rodjenja date check (not null),
    adresa_ambulante character varying(50) check (not null),
    mjesto_ambulante character varying(20) check (not null),
    id_bolnice int check (not null), -- id najblize zdravstvene ustanove
    password_hash character varying(20) check (not null), -- lozinka za prijavu u sustav
    constraint pkLijecnik primary key (oib),
    constraint fkLijecnik foreign key (id_bolnice) references nbp_bolnica(id)
);

create table nbp_pacijent(
    oib char(11) not null check (oib ~ '^[0-9]{11}$'),
    mbo char(9), --maticni broj osiguranika, ako pacijent ima zdravstveno
    ime character varying(20) check (not null),
    prezime character varying(20) check (not null),
    datum_rodjenja date check (not null),
    adresa character varying(50) check (not null), -- trenutna adresa boravista
    mjesto character varying(20) check (not null), -- trenutno mjesto boravista
    oib_lijecnika char(11) not null check (oib_lijecnika ~ '^[0-9]{11}$'), -- oib lijecnika opce prakse
    password_hash character varying(20) check (not null), -- lozinka za prijavu u sustav
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

create table nbp_zahtjev(
    oib_pacijenta char(11) check (not null),
    oib_stari char(11) check (not null), --trenutni liječnik
    oib_novi char(11) check (not null), --željeni novi liječnik
    constraint pkZahtjevi primary key (oib_pacijenta,oib_stari,oib_novi)
);

create table nbp_zahtjev_pretraga(
    oib_pacijenta char(11) check (not null),
    oib_lijecnika char(11) check (not null),
    vrsta char varying(30) check (not null),
    constraint pkZahtjeviPretraga primary key (oib_pacijenta,oib_lijecnika,vrsta)
);
---------------------------------------------------------------------

-- indeksi za ubrzavanje upita

CREATE INDEX pacijent_ime_idx ON nbp_pacijent(prezime, ime);
CREATE INDEX termin_pacijent_idx ON nbp_termin(oib_pacijenta);

---------------------------------------------------------------------
--funkcije

-- zracna udaljenost gradova
CREATE FUNCTION udaljenost(mjesto1 character varying(25), mjesto2 character varying(25))
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
$$ LANGUAGE plpgsql;

-- punjenje tablice susjedi
CREATE FUNCTION punjenje_susjedi()
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

    RETURN 'Ispunjena tablica SUSJEDI.';
END;
$$ LANGUAGE plpgsql;

SELECT * FROM punjenje_susjedi();
select * from nbp_susjedi;

-- popis pacijenata
CREATE FUNCTION popis_pacijenata(oib_L CHAR(11))
    RETURNS table (
        prezime_pacijenta CHAR VARYING(20),
        ime_pacijenta CHAR VARYING(20),
        oib_pacijenta CHAR(11),
        mbo_pacijenta CHAR(9)
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

-- povijest pretraga
CREATE FUNCTION povijest_pretraga(oib CHAR(11))
    RETURNS table (
        datum_pretrage DATE,
        vrsta_pretrage CHAR VARYING (20),
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
        prezime_pacijenta CHAR VARYING(20),
        ime_pacijenta CHAR VARYING(20),
        oib_pacijenta CHAR(11),
        mbo_pacijenta CHAR(9)
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
        datum_termina DATE,
        vrijeme_termina TIME,
        oib CHAR(11)
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
        WHERE datum >= current_date
          AND vrijeme > localtime
          AND id_bolnice = v_id_bolnice
          AND id_pretrage = v_id_pretrage
    ORDER BY datum, vrijeme;
END;
$$ LANGUAGE plpgsql;

-- prvi slobodan termin
CREATE FUNCTION prvi_termin (ime_bolnice CHAR VARYING(30), vrsta_P CHAR VARYING(20))
    RETURNS table (
        datum_termina DATE,
        vrijeme_termina TIME
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
END;
$$ LANGUAGE plpgsql;

---------------------------------------------------------------------

-- ubacivanje testnih podataka

insert into nbp_bolnica values
    (1, 'Opća bolnica "Dr. Ivo Pedišić" Sisak', 'J.J. Strossmayera 59', 'Sisak'),
    (2, 'Opća bolnica "Dr. Josip Benčević" Slavonski Brod',	'Andrije Štampara 42', 'Slavonski Brod'),
    (3, 'Opća bolnica "Dr. Tomislav Bardek" Koprivnica', 'Željka Selingera bb', 'Koprivnica'),
    (4, 'Opća bolnica "Hrvatski ponos" Knin', 'Svetoslava Suronje 12', 'Knin'),
    (5, 'Opća bolnica Bjelovar', 'Mihanovićeva 8', 'Bjelovar'),
    (6, 'Opća bolnica Dubrovnik', 'Roka Mišetića 2', 'Dubrovnik'),
    (7, 'Opća bolnica Gospić',	'Kaniška 111', 'Gospić'),
    (8, 'Opća bolnica Karlovac', 'Andrije Štampara 3', 'Karlovac'),
    (9, 'Opća bolnica Ogulin',	'Bolnička 38', 'Ogulin'),
    (10, 'Opća bolnica Pula', 'Aldo Negri 6', 'Pula'),
    (11, 'Opća bolnica Šibensko-kninske županije', 'Stjepana Radića 83', 'Šibenik'),
    (12, 'Opća bolnica Varaždin', 'I. Meštrovića bb', 'Varaždin'),
    (13, 'Opća bolnica Vinkovci', 'Zvonarska 57', 'Vinkovci'),
    (14, 'Opća bolnica Virovitica',	'Ljudevita Gaja 21', 'Virovitica'),
    (15, 'Opća bolnica Zabok', 'Bračak 8', 'Zabok'),
    (16, 'Opća bolnica Zadar', 'Bože Peričića 5', 'Zadar'),
    (17, 'Opća županijska bolnica Našice', 'Bana Jelačića 10', 'Našice'),
    (18, 'Opća županijska bolnica Požega', 'Osječka 107', 'Požega'),
    (19, 'Opća županijska bolnica Vukovar',	'Županijska 35', 'Vukovar'),
    (20, 'Županijska bolnica Čakovec', 'I. G. Kovačića 1E' ,'Čakovec'),
    (21, 'Klinička bolnica "Dubrava"', 'Avenija Gojka Šuška 6', 'Zagreb'),
    (22, 'Klinička bolnica "Sveti Duh"', 'Sveti Duh 64', 'Zagreb'),
    (23, 'Klinička bolnica "Merkur"', 'Zajčeva 19', 'Zagreb'),
    (24, 'Klinika za dječje bolesti', 'Klaićeva 16', 'Zagreb'),
    (25, 'Klinika za infektivne bolesti "Dr. Fran Mihaljević"',	'Mirogojska 8', 'Zagreb'),
    (26, 'Klinika za ortopediju Lovran', 'Šetalište Maršala Tita 1', 'Lovran'),
    (27, 'Klinika za psihijatriju Vrapče', 'Bolnička cesta 32', 'Zagreb'),
    (28, 'Magdalena - Klinika za kardiovaskularne bolesti Med.fakulteta u Osijeku',	'Ljudevita Gaja 2', 'Krapinske Toplice'),
    (29, '"Biokovka" specijalna bolnica za medicinsku rehabilitaciju - Makarska', 'Put Cvitačke 9', 'Makarska'),
    (30, '"Kalos" Specijalna bolnica za medicinsku rehabilitaciju Vela Luka', 'Obala 3 br. 3', 'Vela Luka'),
    (31, 'Bolnica za ortopedsku kirurgiju i rehabilitaciju "Prim.dr. Martin Horvat" Rovinj', 'Ulica Luigi Monti 2', 'Rovinj');

insert into nbp_bolnica values
    (32, 'Opća bolnica "Dr. Josip Benčević" Slavonski Brod',	'Andrije Štampara 42', 'Slavonski Brod'),
    (33, 'Dječja bolnica Srebrnjak', 'Srebrnjak 100', 'Zagreb'),
    (34, 'Neuropsihijatrijska bolnica "Dr. Ivan Barbot" Popovača', 'Jelengradska 1', 'Popovača'),
    (35, 'Psihijatrijska bolnica "Sveti Ivan"', 'Jankomir 11', 'Zagreb'),
    (36, 'Psihijatrijska bolnica "Sveti Rafael" Strmac', 'Šumetlica 87', 'Cernik'),
    (37, 'Psihijatrijska bolnica Lopača', 'Lopača 11', 'Dražice'),
    (38, 'Psihijatrijska bolnica Rab', 'Kampor 224', 'Rab'),
    (39, 'Psihijatrijska bolnica Ugljan', 'Otočkih dragovoljaca 42', 'Ugljan'),
    (40, 'Psihijatrijska bolnica za djecu i mladež', 'Ivana Kukuljevića 11', 'Zagreb'),
    (41, 'Specijalna bolnica za kronične bolesti dječje dobi Gornja Bistra', 'Bolnička 21', 'Gornja Bistra'),
    (42, 'Specijalna bolnica za medicinsku rehabilitaciju "Naftalan" Ivanić Grad', 'Omladinska 23a', 'Ivanić Grad'),
    (43, 'Specijalna bolnica za medicinsku rehabilitaciju Daruvarske Toplice', 'Julijev park 1', 'Daruvar'),
    (44, 'Specijalna bolnica za medicinsku rehabilitaciju Krapinske Toplice', 'Gajeva 2', 'Krapinske Toplice'),
    (45, 'Specijalna bolnica za medicinsku rehabilitaciju Lipik', 'Marije Terezije 13', 'Lipik'),
    (46, 'Specijalna bolnica za medicinsku rehabilitaciju Stubičke Toplice', 'Park Matije Gupca 1', 'Stubičke toplice'),
    (47, 'Specijalna bolnica za medicinsku rehabilitaciju Varaždinske Toplice', 'Trg Slobode 1', 'Varaždinske Toplice'),
    (48, 'Specijalna bolnica za ortopediju Biograd na Moru', 'Zadarska 62', 'Biograd na Moru'),
    (49, 'Specijalna bolnica za plućne bolesti', 'Rockefellerova 3', 'Zagreb'),
    (50, 'Specijalna bolnica za produženo liječenje - Duga Resa', 'Jozefa Jeruzalema 7', 'Duga Resa'),
    (51, 'Specijalna bolnica za zaštitu djece s neurorazvojnim i motoričkim smetnjama', 'Goljak 2', 'Zagreb'),
    (52, 'Specijalna bolnica za medicinsku rehabilitaciju Primorsko-goranske županije', 'Gajevo šetalište 21', 'Crikvenica'),
    (53, 'Specijalna bolnica za medicinsku rehabilitaciju bolesti srca, pluća i reumatizma', 'Maršala Tita 188/1', 'Opatija');

insert into nbp_bolnica values
    (54, 'Poliklinika za bolesti dišnog sustava', 'Prilaz Baruna Filipovića 11', 'Zagreb'),
    (55, 'Poliklinika za fizikalnu medicinu i rehabilitaciju dr. Drago Čop', 'Antuna Mihanovića 3', 'Zagreb'),
    (56, 'Poliklinika za prevenciju kardiovaskularnih bolesti i rehabilitaciju', 'Draškovićeva 13', 'Zagreb'),
    (57, 'Poliklinika za rehabilitaciju slušanja i govora Suvag', 'Kneza Ljudevita Posavskog 10', 'Zagreb'),
    (58, 'Poliklinika za rehabilitaciju slušanja i govora Suvag', 'J.J. Strossmayera 6', 'Osijek'),
    (59, 'Poliklinika za rehabilitaciju slušanja i govora Suvag', 'dr. Vladka Mačeka 48', 'Karlovac'),
    (60, 'Stomatološka poliklinika Zagreb', 'Perkovčeva 3', 'Zagreb'),
    (61, 'Stomatološka poliklinika Split', 'A. G. Matoša 2', 'Split'),
    (62, 'Poliklinika za zaštitu djece grada Zagreba', 'Đorđićeva 26', 'Zagreb'),
    (63, 'Poliklinika za rehabilitaciju osoba sa smetnjama u razvoju Split', 'Put Meja 5', 'Split'),
    (64, 'Poliklinika za fizikalnu medicinu i rehabilitaciju Velika Gorica', 'Matice Hrvatske bb', 'Velika Gorica'),
    (65, 'Dom zdravlja Bjelovarsko-bilogorske županije', 'J. Jelačića 13 c', 'Bjelovar'),
    (66, 'Dom zdravlja Čakovec', 'I. G. Kovačića 1e', 'Čakovec'),
	(67, 'Dom zdravlja Dubrovnik', 'Dr. A. Starčevića 1', 'Dubrovnik'),
	(68, 'Dom zdravlja Donji Miholjac', 'Trg Ante Starčevića 25', 'Donji Miholjac'),
    (69, 'Dom zdravlja Beli Manastir', 'Školska 5', 'Beli Manastir'),
    (70, 'Dom zdravlja Drniš', 'Josipa Kosora 14', 'Drniš'),
    (71, 'Dom zdravlja Đakovo', 'Petra Preradovića 2', 'Đakovo'),
    (72, 'Dom zdravlja Gospić', '118. brigade HV 3', 'Gospić'),
    (73, 'Dom zdravlja Karlovac', 'D.V. Mačeka 48', 'Karlovac'),
    (74, 'Dom zdravlja Knin', 'Kneza I. Nelipića 1', 'Knin'),
    (75, 'Dom zdravlja Koprivničko-križevačke županije', 'Trg Tomislava Bardeka 10', 'Koprivnica'),
    (76, 'Dom zdravlja Korčula', 'Ulica 57 k. br. 5', 'Korčula'),
    (77, 'Dom zdravlja Korenica', 'Zagrebačaka 41', 'Korenica'),
    (78, 'Dom zdravlja Krapinsko-zagorske županije', 'Dr. M. Crkvenca 1', 'Krapina'),
    (79, 'Dom zdravlja Kutina', 'A.G. Matoša 42', 'Kutina'),
    (80, 'Dom zdravlja Metković', 'Dr. A. Starčevića 12', 'Metković'),
    (81, 'Dom zdravlja MUP-a', 'Šarengradska 3', 'Zagreb'),
    (82, 'Dom zdravlja Našice', 'Bana Josipa Jelačića 10', 'Našice'),
    (83, 'Dom zdravlja „dr.A.Štampar“ Nova Gradiška', 'Relkovićeva 7', 'Nova Gradiška'),
    (84, 'Dom zdravlja Novalja', 'Špital 1', 'Novalja'),
    (85, 'Dom zdravlja Ogulin', 'B.Frankopana 14', 'Ogulin'),
    (86, 'Dom zdravlja Osijek', 'Park kralja Petra Krešimira IV br. 6', 'Osijek'),
    (87, 'Dom zdravlja Ozalj', 'Kolodvorska 2', 'Ozalj'),
    (88, 'Dom zdravlja Petrinja', 'Matije Gupca 4', 'Petrinja'),
    (89, 'Dom zdravlja Ploče', 'Kralja Tomislava 9', 'Ploče'),
    (90, 'Dom zdravlja Požeško-slavonske županije', 'M. Gupca 10', 'Požega'),
    (91, 'Dom zdravlja Primorsko-goranske županije', 'M. Gupca 10', 'Požega'),
    (92, 'Dom zdravlja Požeško-slavonske županije', 'Krešimirova 52/a', 'Rijeka'),
    (93, 'Dom zdravlja Senj', 'Stara cesta 43', 'Senj'),
    (94, 'Dom zdravlja Slavonski Brod', 'Borovska 7', 'Slavonski Brod'),
    (95, 'Dom zdravlja Slunj', 'Plitvička 18a', 'Slunj'),
    (96, 'Dom zdravlja Splitsko-dalmatinske županije', 'Kavanjinova 2', 'Split'),
    (97, 'Dom zdravlja Šibenik', 'Stjepana Radića 83', 'Šibenik'),
    (98, 'Dom zdravlja Valpovo', 'Kralja Petra Krešimira IV br. 4', 'Valpovo'),
    (99, 'Dom zdravlja Varaždinske županije', 'Kolodvorska 20', 'Varaždin'),
    (100, 'Dom zdravlja „Dr. A. Franulović“ Vela Luka', 'Ulica 1 br. 1', 'Vela Luka'),
    (101, 'Dom zdravlja Virovitičko-podravske županije', 'Gajeva 21', 'Virovitica'),
    (102, 'Dom zdravlja Vojnić', 'A.Hebranga 24', 'Vojnić'),
    (103, 'Dom zdravlja Zagreb - Centar', 'Runjaninova 4', 'Zagreb'),
    (104, 'Dom zdravlja Zagreb - Zapad', 'Prilaz baruna Filipovića 11', 'Zagreb'),
    (105, 'Dom zdravlja Zagrebačke županije', 'Ljudevita Gaja 37', 'Samobor'),
    (106, 'Dom zdravlja Zagreb - Istok', 'Švarcova 20', 'Zagreb'),
    (107, 'Dom Zadarske županije', 'Ivana Mažuranića 28a', 'Zadar'),
    (108, 'Istarski domovi zdravlja', 'Flanatička 27', 'Pula'),
    (109, 'Dom zdravlja Otočac', 'Vladimira Nazora 14', 'Otočac'),
    (110, 'Dom zdravlja Duga Resa', 'Bana J. Jelačića 4', 'Duga Resa'),
    (111, 'Dom zdravlja Vinkovci', 'Kralja Zvonimira 53', 'Vinkovci'),
    (112, 'Dom zdravlja Vukovar', 'Sajmište 1', 'Vukovar'),
    (113, 'Dom zdravlja Županja', 'dr. Franje Račkog 32', 'Županja'),
    (114, 'Dom zdravlja Sisak', 'Kralja Tomislava 1', 'Sisak');

select * from nbp_bolnica;

select distinct mjesto from nbp_bolnica;

insert into nbp_mjesto values
    ('Senj', 44.98944, 14.90583),
    ('Split', 43.50891, 16.439154),
    ('Gospić', 44.54611, 15.37472),
    ('Rab', 44.75694, 14.76083),
    ('Cernik', 45.28861, 17.38194),
    ('Novalja', 44.55778, 14.8866),
    ('Popovača', 45.56972, 16.625),
    ('Virovitica', 45.83194, 17.38389),
    ('Varaždinske Toplice', 46.20917, 16.41917),
    ('Vinkovci', 45.28833, 18.80472),
    ('Daruvar', 45.59056, 17.225),
    ('Kutina', 45.475, 16.78194),
    ('Gornja Bistra', 45.91667, 15.9),
    ('Dubrovnik', 42.64125, 18.10909),
    ('Slunj', 45.11456, 15.5843),
    ('Duga Resa', 45.44614, 15.49871),
    ('Knin', 44.04063, 16.19662),
    ('Vela Luka', 42.96333, 16.7225),
    ('Našice', 45.48861, 18.08778),
    ('Krapina', 46.16083, 15.87889),
    ('Lovran', 45.29194, 14.27417),
    ('Korenica', 44.74389, 15.70972),
    ('Zadar', 44.11578, 15.22514),
    ('Bjelovar', 45.89861, 16.84889),
    ('Korčula', 42.96038, 17.13525),
    ('Opatija', 45.33658, 14.30782),
    ('Osijek', 45.55111, 18.69389),
    ('Crikvenica', 45.17722, 14.69278),
    ('Samobor', 45.80306, 15.71806),
    ('Vojnić', 45.32361, 15.69861),
    ('Šibenik', 43.73429, 15.8942),
    ('Stubičke toplice', 45.97585, 15.93238),
    ('Petrinja', 45.4375, 16.29),
    ('Donji Miholjac', 45.76083, 18.16722),
    ('Rijeka', 45.32673, 14.44241),
    ('Beli Manastir', 45.77, 18.60361),
    ('Dražice', 45.39083, 14.47028),
    ('Valpovo', 45.66083, 18.41861),
    ('Zagreb', 45.81444, 15.97798),
    ('Vukovar', 45.35161, 19.00225),
    ('Đakovo', 45.30833, 18.41056),
    ('Rovinj', 45.08268, 13.63457),
    ('Požega', 45.34028, 17.68528),
    ('Metković', 43.05417, 17.64833),
    ('Čakovec', 46.38444, 16.43389),
    ('Ogulin', 45.26611, 15.22861),
    ('Karlovac', 45.49167, 15.55),
    ('Županja', 45.0775, 18.6975),
    ('Ivanić Grad', 45.70833, 16.39694),
    ('Nova Gradiška', 45.255, 17.38306),
    ('Velika Gorica', 45.7125, 16.07556),
    ('Slavonski Brod', 45.16028, 18.01556),
    ('Otočac', 44.86944, 15.2375),
    ('Drniš', 43.8625, 16.15556),
    ('Makarska', 43.29694, 17.01778),
    ('Sisak', 45.46611, 16.37833),
    ('Ozalj', 45.61293, 15.47771),
    ('Zabok', 46.02626, 15.90391),
    ('Varaždin', 46.30444, 16.33778),
    ('Lipik', 45.41139, 17.15222),
    ('Ploče', 43.05611, 17.43278),
    ('Ugljan', 44.13083, 15.10306),
    ('Krapinske Toplice', 46.09333, 15.84333),
    ('Pula', 44.86833, 13.84806),
    ('Biograd na Moru', 43.94333, 15.45194),
    ('Koprivnica', 46.16278, 16.8275)
;

insert into nbp_lijecnik values
    (10000444444, 'Gandalf', 'Mithrandir', '1940-07-02', 'Glavna 8', 'Bree', 1, 1234567890),
    (10000291105, 'Aragorn', 'Elessar', '1980-07-02', 'Glavna 1', 'Minas Tirith', 4, 1234567890),
    (10000062905, 'Legolas', 'Thranduilion', '1970-07-02', 'Glavna 7', 'Mirkwood', 14, 1234567890),
    (10000999919, 'Theoden', 'Ednew', '1960-07-02', 'Glavna 5', 'Edoras', 17, 1234567890),
    (10000857999, 'Arwen', 'Undomiel', '1970-07-02', 'Glavna 4', 'Rivendell', 20, 1234567890),
    (10000893743, 'Denethor', 'Ecthelion', '1950-07-02', 'Glavna 9', 'Minas Tirith', 28, 1234567890),
    (10000891233, 'Tom', 'Bombadil', '1940-07-02', 'Glavna 2', 'Bree', 56, 1234567890),
    (10000213905, 'Elendil', 'Voronda', '1960-07-02', 'Glavna 11', 'Numenor', 70, 1234567890),
    (10000294736, 'Feanor', 'Curufinwe', '1960-07-02', 'Glavna 12', 'Tirion', 13, 1234567890),
    (10000243905, 'Maedhros', 'Nelyafinwe', '1980-07-02', 'Glavna 6', 'Tirion', 27, 1234567890),
    (10000432043, 'Finwe', 'Noldoran', '1940-07-02', 'Glavna 13', 'Tirion', 100, 1234567890),
    (10000794735, 'Elrond', 'Peredhel', '1940-07-02', 'Glavna 3', 'Rivendell', 114, 1234567890);

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

select * from popis_pacijenata('10000444444');

insert into nbp_pretraga values
    (1, 'dermatološki pregled', 45),
    (2, 'oftalmološki pregled', 45),
    (3, 'fizikalna medicina', 45),
    (4, 'magnetska rezonanca', 50),
    (5, 'serologija', 10),
    (6, 'dijabetologija', 30);


insert into nbp_bolnica_pretraga values
    (95, 2),
    (102, 3),
    (105, 5),
    (17, 4),
    (110, 1),
    (102, 1),
    (4, 3),
    (55, 3),
    (106, 4),
    (69, 1),
    (37, 1),
    (62, 4),
    (112, 6),
    (7, 5),
    (86, 6),
    (6, 1),
    (109, 4),
    (85, 2),
    (108, 6),
    (93, 6),
    (87, 6),
    (57, 1),
    (100, 6),
    (26, 1),
    (16, 5),
    (77, 4),
    (24, 1),
    (5, 5),
    (91, 2),
    (63, 2),
    (72, 2),
    (102, 6),
    (109, 3),
    (61, 3),
    (91, 5),
    (9, 5),
    (4, 5),
    (76, 3),
    (51, 3),
    (101, 3),
    (6, 3),
    (34, 2),
    (69, 3),
    (21, 5),
    (56, 2),
    (48, 2),
    (72, 4),
    (65, 1),
    (32, 2),
    (55, 1),
    (35, 3),
    (63, 1),
    (82, 1),
    (38, 2),
    (50, 1),
    (63, 3),
    (112, 2),
    (93, 5),
    (17, 6),
    (108, 5),
    (28, 5),
    (82, 3),
    (71, 6),
    (3, 4),
    (51, 6),
    (12, 1),
    (44, 5),
    (68, 5),
    (22, 5),
    (19, 3),
    (28, 4),
    (13, 4),
    (40, 3),
    (66, 4),
    (92, 4),
    (91, 4),
    (87, 1),
    (21, 6),
    (46, 6),
    (81, 3),
    (100, 2),
    (33, 3),
    (1, 5),
    (39, 3),
    (46, 3),
    (59, 1),
    (45, 1),
    (88, 5),
    (18, 2),
    (89, 2),
    (14, 4),
    (59, 2),
    (41, 4),
    (65, 6),
    (17, 2),
    (15, 3),
    (105, 2),
    (54, 1),
    (53, 4),
    (52, 3),
    (112, 1),
    (48, 4),
    (50, 6),
    (8, 6),
    (12, 5),
    (37, 5);

insert into nbp_termin VALUES
  (10000338099, 2, '2010-07-02', '12:00', 100),
  (10000338099, 1, '2011-07-02', '11:00', 12),
  (10000917906, 5, '2012-07-02', '09:00', 88),
  (10000917906, 5, '2009-07-02', '15:00', 88),
  (10000917906, 3, '2004-07-02', '12:00', 81),
  (10000395731, 2, '2005-07-02', '16:00', 100),
  (10000395731, 6, '2006-07-02', '11:00', 50),
  (10000998713, 2, '2003-07-02', '12:00', 100),
  (10000520909, 4, '2007-07-02', '12:00', 92),
  (10000013006, 1, '2004-07-02', '13:00', 12),
  (10000013006, 1, '2005-07-02', '16:00', 12),
  (10000013006, 1, '2015-07-02', '17:40', 12),
  (10000013006, 1, '2017-07-02', '12:00', 87),
  (10000878383, 6, '2009-07-02', '11:00', 21),
  (10000402929, 5, '2018-07-02', '10:00', 1),
  (10000402929, 5, '2019-07-02', '12:00', 1),
  (10000338099, 5,'2024-07-02','12:00', 1),
  (10000917906, 3,'2024-07-02','12:00', 81),
  (10000917906, 5,'2024-07-05','12:00', 12),
  (10000395731, 1,'2024-07-07','12:00', 59),
  (10000998713, 1,'2024-07-11','12:00', 59),
  (10000998713, 2,'2024-07-02','12:00', 100),
  (10000520909, 6,'2024-07-03','12:00', 65),
  (10000013006, 2,'2024-07-07','12:00', 18),
  (10000013006, 4,'2024-07-04','12:00', 48),
  (10000013006, 4,'2024-07-05','12:00', 48),
  (10000878383, 4,'2024-07-02','12:00', 53),
  (10000402929, 6,'2024-07-03','12:00', 8);
