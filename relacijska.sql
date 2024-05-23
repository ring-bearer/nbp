-- tablice

create table nbp_pacijent(
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
);

-- lijecnik opce prakse, ne specijalist
create table nbp_lijecnik(
    oib char(11) check (not null),
    ime character varying(20) check (not null),
    prezime character varying(20) check (not null),
    datum_rodjenja date check (not null),
    adresa_ambulante character varying(30) check (not null),
    mjesto_ambulante character varying(20) check (not null),
    constraint pkLijecnik primary key (oib)
);

-- cini mi se nerealno i nepotrebno imati poseban PK ID_pretrage jer bi to u stvarnosti bilo jako puno pretraga i puno prostora treba rezervirati za to
-- npr. vjerojatno int ne bi bilo dosta u nekom trenutku
-- a svakak ne budemo trazili po ID_pretrage vjerojatno
-- mozemo dodati ako skuzimo da je potrebno u nekom trenutku
create table nbp_pretraga(
    oib_pacijenta char(11) check (not null),
    vrsta char varying (20) check (not null),
    datum date check (not null),
    vrijeme time check (not null),
    id_bolnice int check (not null), -- ovo je referenca na PK iz grafovske baze. to ne treba nikak referencirati valjda onda
    constraint pkPretraga primary key (oib_pacijenta, datum, vrijeme),
    constraint fkPretraga foreign key (oib_pacijenta) references nbp_pacijent(oib)
);

---------------------------------------------------------------------

-- indeksi za ubrzavanje upita

CREATE INDEX pacijent_ime_idx ON nbp_pacijent(prezime, ime);
CREATE INDEX pretraga_pacijent_idx ON nbp_pretraga(oib_pacijenta);
CREATE INDEX pretraga_lista_cekanja_idx ON nbp_pretraga(id_bolnice, vrsta);

---------------------------------------------------------------------
--funkcije

-- povijest pretraga
CREATE FUNCTION povijest_pretraga(oib CHAR(11))
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
$$ LANGUAGE plpgsql;

-- popis pacijenata
CREATE FUNCTION popis_pacijenata(oib_L CHAR(11))
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
$$ LANGUAGE plpgsql;


-- lista cekanja
CREATE FUNCTION lista_cekanja(bolnica INT, vrsta_P CHAR VARYING(20))
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
$$ LANGUAGE plpgsql;

-- jos treba jednu za slobodne termine, al to opet treba povlaciti iz grafovske baze

---------------------------------------------------------------------

