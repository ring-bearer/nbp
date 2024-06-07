-- indeksi za ubrzavanje upita

CREATE INDEX pacijent_ime_idx ON nbp_pacijent(prezime, ime);
CREATE INDEX termin_pacijent_idx ON nbp_termin(oib_pacijenta);
CREATE INDEX susjedi_bolnica_idx ON nbp_susjedi(id_bolnice1);

---------------------------------------------------------------------

-- funkcije

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

-- punjenje tablice susjedi - namjerno dvaput svaki par
CREATE FUNCTION punjenje_susjedi()
    RETURNS text
AS $$
DECLARE
    v_bolnica1 RECORD;
    v_bolnica2 RECORD;
BEGIN
    FOR v_bolnica1 IN
        SELECT *
            FROM nbp_bolnica
    LOOP
        FOR v_bolnica2 IN
            SELECT *
                FROM nbp_bolnica
                    WHERE nbp_bolnica.id <> v_bolnica1.id
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
        ime_bolnice  CHAR VARYING (30),
        mjesto_bolnice CHAR VARYING (20)
    )
AS $$
BEGIN
    RETURN QUERY
        SELECT datum, vrsta, ime, mjesto
            FROM nbp_termin
                LEFT JOIN nbp_bolnica
                    ON id = id_bolnice
                LEFT JOIN nbp_pretraga
                    ON nbp_termin.id_pretrage = nbp_pretraga.id
        WHERE oib_pacijenta = oib
        ORDER BY datum DESC; -- najnovije pretrage prve
END;
$$ LANGUAGE plpgsql;

-- lista cekanja
CREATE FUNCTION lista_cekanja(ime_bolnice CHAR VARYING(30), mjesto_bolnice CHAR VARYING(20), vrsta_P CHAR VARYING(20))
    RETURNS table (
        datum_termina TEXT,
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
        WHERE ime = ime_bolnice
          AND mjesto = mjesto_bolnice;

    SELECT id INTO v_id_pretrage
        FROM nbp_pretraga
        WHERE vrsta = vrsta_P;

    RETURN QUERY
        SELECT to_char(datum, 'dd.mm.yyyy.'), vrijeme, oib_pacijenta
            FROM nbp_termin
        WHERE id_bolnice = v_id_bolnice
          AND id_pretrage = v_id_pretrage
          AND (datum > current_date OR (datum = current_date AND vrijeme > localtime))
    ORDER BY datum, vrijeme;
END;
$$ LANGUAGE plpgsql;

-- prvi slobodan termin
CREATE FUNCTION prvi_termin (ime_bolnice CHAR VARYING(30), mjesto_bolnice CHAR VARYING(20), vrsta_P CHAR VARYING(20))
    RETURNS table (
        datum_termina TEXT,
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
        WHERE ime = ime_bolnice
          AND mjesto = mjesto_bolnice;

    SELECT id, trajanje_min INTO v_id_pretrage, v_trajanje
        FROM nbp_pretraga
        WHERE vrsta = vrsta_P;

    SELECT datum, vrijeme INTO v_datum, v_vrijeme
    FROM (
        SELECT datum, vrijeme
            FROM nbp_termin
            WHERE id_pretrage = v_id_pretrage
              AND id_bolnice = v_id_bolnice
              AND datum > current_date
            ORDER BY datum DESC, vrijeme DESC
            LIMIT 1) AS tablica;

    IF v_datum IS NULL
        THEN
            IF to_char(current_date, 'Day') = 'Friday' -- u ponedjeljak, ne u subotu
                THEN
                    RETURN QUERY
                        SELECT to_char(current_date + INTERVAL '3 days', 'dd.mm.yyyy.') AS datum, '7:00'::TIME AS vrijeme;
            ELSIF to_char(current_date, 'Day') = 'Saturday' -- u ponedjeljak, ne u nedjelju
                THEN
                    RETURN QUERY
                        SELECT to_char(current_date + INTERVAL '2 days', 'dd.mm.yyyy.') AS datum, '7:00'::TIME AS vrijeme;
            ELSE -- iduci dan
                RETURN QUERY
                    SELECT to_char(current_date + INTERVAL '1 day', 'dd.mm.yyyy.') AS datum, '7:00'::TIME AS vrijeme;
            END IF;
    ELSIF v_vrijeme + v_trajanje * INTERVAL '1 minute' <= '18:00'::TIME
        THEN
            RETURN QUERY
                SELECT to_char(v_datum, 'dd.mm.yyyy.') AS datum, v_vrijeme + v_trajanje * INTERVAL '1 minute' AS vrijeme;
    ELSE
        IF to_char(current_date, 'Day') <> 'Friday'
            THEN
                RETURN QUERY
                    SELECT to_char(v_datum + INTERVAL '1 day', 'dd.mm.yyyy.') AS datum, '7:00'::TIME AS vrijeme;
        ELSE
            RETURN QUERY
                SELECT to_char(v_datum + INTERVAL '3 days', 'dd.mm.yyyy.') AS datum, '7:00'::TIME AS vrijeme;
        END IF;
    END IF;
END;
$$ LANGUAGE plpgsql;