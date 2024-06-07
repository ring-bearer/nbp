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
    oib_pacijenta char(11) not null, -- nije strani kljuc, ne mora pacijent biti prijavljen u aplikaciju
    id_pretrage int check (not null),
    datum date check (not null),
    vrijeme time check (not null),
    id_bolnice int check (not null),
    constraint pkTermin primary key (oib_pacijenta, datum, vrijeme),
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
