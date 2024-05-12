create table nbp_doktor(
    oib char(11) not null,
    ime character varying(20) not null,
    prezime character varying(20) not null,
    id_bolnica int,
    placa int,
    podrucje character varying(20), --ono za sto je specijaliziran
    specijalizant boolean, --0 ako je vec zavrsio spec, inace 1
    constraint pkDoktor primary key (oib),
    constraint fkBolnica foreign key (id_bolnica) references nbp_bolnica(id)
);

create table nbp_doktor(
    oib char(11) not null,
    ime varchar (20) not null,
    prezime varchar (20) not null,
    id_bolnica int,
    placa int,
    podrucje varchar(20),
    specijalizant boolean,
    constraint pkDoktor primary key (oib),
    constraint fkBolnica foreign key (id_bolnica) references nbp_bolnica(id)
);

insert into nbp_doktor values
    (10000444444, 'Gandalf', 'Mithrandir', 05, 5000, 'neonatologija' , false),
    (10000291105, 'Aragorn', 'Elessar', 01, 2000, 'hematologija', true),
    (10000062905, 'Legolas', 'Thranduilion', 04, 3000, 'oftalmologija', true),
    (10000999919, 'Theoden', 'Ednew', 02, 2000, 'fizikalna medicina' , true),
    (10000857999, 'Arwen', 'Undomiel', 03, 5000, 'dermatologija' , false),
    (10000893743, 'Denethor', 'Ecthelion', 01, 2000, 'kirurgija' , true),
    (10000891233, 'Tom', 'Bombadil', 05, 6000, 'psihijatrija' , false),
    (10000213905, 'Elendil', 'Voronda', 06, 5000, 'hematologija', false),
    (10000294736, 'Feanor', 'Curufinwe', 07, 6500, 'kirurgija' , false),
    (10000243905, 'Maedhros', 'Nelyafinwe', 07, 3000, 'fizikalna medicina', true),
    (10000432043, 'Finwe', 'Noldoran', 07, 6000, 'neonatologija', false),
    (10000794735, 'Elrond', 'Peredhel', 03, 5000, 'oftalmologija', false);

create table nbp_bolnica(
    id int check (not null),
    ime character varying(20) check (not null),
    --ovo su GPS koordinate
    zemlj_sirina float
        constraint chkSirina
            check (zemlj_sirina >= -90 and zemlj_sirina<=90),
    zemlj_duzina float
        constraint chkDuzina
            check (zemlj_duzina >= -180 and zemlj_duzina<=180),
    constraint pkBolnica primary key (id)
);

insert into nbp_bolnica values
    (01, 'Minas Tirith', 0, 0),
    (02, 'Edoras', -5, -40),
    (03, 'Rivendell', -50, -5),
    (04, 'Mirkwood', -55, 60),
    (05, 'Bree', -55, -10),
    (06, 'Numenor', -40, 60),
    (07, 'Tirion', -80, -150);

create table nbp_pacijent(
    oib char(11) not null,
    mbo char(9), --maticni broj osiguranika, ako pacijent ima zdravstveno
    ime character varying(20) not null,
    prezime character varying(20) not null,
    constraint pkPacijent primary key (oib)
);

drop table nbp_pacijent2;
drop table nbp_pacijent;

create table nbp_pacijent(
    oib char(11) check (not null),
    mbo char(9), --maticni broj osiguranika, ako pacijent ima zdravstveno
    ime character varying(20) check (not null),
    prezime character varying(20) check (not null),
    constraint pkPacijent primary key (oib)
);

insert into nbp_pacijent values
    (10000338099, 100338099, 'Frodo', 'Baggins'),
    (10000917906, 100917906, 'Samwise', 'Gamgee'),
    (10000998713, 100998713, 'Meriadoc', 'Brandybuck'),
    (10000395731, 100395731, 'Peregrin', 'Took'),
    (10000520909, 100520909, 'Boromir', 'Echtelion'),
    (10000013006, 100013006, 'Faramir', 'Echtelion'),
    (10000878383, 100878383, 'Eowyn', 'Eadig'),
    (10000402929, 100402929, 'Eomer', 'Eadig');

