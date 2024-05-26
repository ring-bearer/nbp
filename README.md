# Projekt iz NBP

Web aplikaciji moze se pristupiti sa lokalnog hosta.
Potrebno je imati PHP i instaliran driver za njegov rad s Postgresom.\
Korisne naredbe (na Linuxu preko terminala):
- instalacija PHP-a: ```sudo apt-get install php```
- provjera instalirane verzije PHP-a: ```php --version```
- instalacija drivera za Postgres: ```sudo apt-get install php-pgsql``` ili ako imate npr. PHP verziju 7.4, ```sudo apt-get install php7.4-pgsql```
- pronalazak datoteke php.ini: ```sudo find / -name php.ini```

U slučaju da Postgres driver ne radi nakon instalacije, moguće je da je u php.ini potrebno odkomentirati linije koje počinju s ```extension=php_pgsql```.

Za relacijsku bazu, povezujemo se s bazom odbojka kao za prvu i drugu zadaću. Nakon dobivanja konekcije s virtualkom na VCLu, potrebno je dobiveni IP prekopirati u app/database/db.class.php, kod imena host-a.
Tad se za brzu inicijalizaciju baze može pristupiti prepareDB.php preko web-a (adresa oblika http://localhost/~username/nbp/app/database/prepareDB.php).
Nakon toga, korisnik bi uvijek trebao pristupati samo datoteci index.php, koja će ga preusmjeriti na traženi sadržaj (http://localhost/~username/nbp/index.php).
