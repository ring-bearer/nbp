# Projekt iz NBP

Web aplikaciji moze se pristupiti sa lokalnog hosta ili prebacivanjem na studenta.
Korisnik bi uvijek trebao pristupati samo index.php.
Potrebno je imati PHP i instaliran driver za njegov rad s Postgresom
(na linuxu preko terminala komanda sudo apt-get install php-pgsql).

Za relacijsku bazu, povezujemo se s bazom odbojka kao za prvu i drugu zadaću.
Nakon dobivanja konekcije s virtualkom na VCLu, potrebno je dobiveni IP
prekopirati u app/database/db.class.php, kod imena host-a.
Tad se za brzu inicijalizaciju baze moze pristupiti prepareDB.php preko web-a,
nakon cega bi se trebale izvrsiti naredbe koje su prvo pisane u projekt.sql,
te prenesene u prepareDB da ga PHP shvati.