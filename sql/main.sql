/****************************************************************/
/*                                                              */
/*                                                              */
/*              Vytvoření základních tabulek                    */
/*                                                              */
/*                                                              */
/****************************************************************/

-- Tabulka: Uživatelské role
CREATE TABLE uzivatelska_role (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
    ,nazev VARCHAR(50) NOT NULL UNIQUE
);
INSERT INTO uzivatelska_role (nazev) VALUE ('neregistrovany uzivatel');
INSERT INTO uzivatelska_role (nazev) VALUE ('uzivatel');
INSERT INTO uzivatelska_role (nazev) VALUE ('administrator');

-- Tabulka: Uživatele
CREATE TABLE uzivatele (
    id INTEGER PRIMARY KEY AUTO_INCREMENT
    ,role_id INTEGER REFERENCES uzivatelska_role(id) 
    ,prezdivka VARCHAR(20) NOT NULL
    ,heslo VARCHAR(32) NOT NULL
);
-- Heslo administrátora: '@admin'
INSERT INTO uzivatele (role_id,prezdivka,heslo) VALUE (3,'admin','24b0712e91489671013c3bc67d4ec894');

-- Tabulka: Veličiny
CREATE TABLE veliciny (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
    ,nazev VARCHAR(50) NOT NULL UNIQUE
);
INSERT INTO veliciny (nazev) VALUE ('délka');
INSERT INTO veliciny (nazev) VALUE ('čas');

-- Tabulka: Jednotky
CREATE TABLE jednotky (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
    ,velicina_id INTEGER REFERENCES veliciny(id)
    ,nazev VARCHAR(50) NOT NULL UNIQUE
    ,zkratka VARCHAR(4) NOT NULL UNIQUE
);
INSERT INTO jednotky (velicina_id,nazev,zkratka) VALUE (1,'metr','m');          -- 1
INSERT INTO jednotky (velicina_id,nazev,zkratka) VALUE (1,'centimetr','cm');    -- 2
INSERT INTO jednotky (velicina_id,nazev,zkratka) VALUE (2,'minuta','min');      -- 3
INSERT INTO jednotky (velicina_id,nazev,zkratka) VALUE (2,'hodina','h');        -- 4

-- Tabulka: Převody
CREATE TABLE prevody (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
    ,zdroj_jednotka_id INTEGER REFERENCES jednotky(id)
    ,cil_jednotka_id INTEGER REFERENCES jednotky(id)
    ,koeficient DECIMAL(19,9) NOT NULL
);
INSERT INTO prevody (zdroj_jednotka_id,cil_jednotka_id,koeficient) VALUE (1,2,100);         -- 1m = 100cm
INSERT INTO prevody (zdroj_jednotka_id,cil_jednotka_id,koeficient) VALUE (2,1,0.01);        -- 1cm = 0.01m
INSERT INTO prevody (zdroj_jednotka_id,cil_jednotka_id,koeficient) VALUE (3,4,0.017);       -- 1min = 0.017h
INSERT INTO prevody (zdroj_jednotka_id,cil_jednotka_id,koeficient) VALUE (4,3,60);          -- 1h = 60min


-- Tabulka: Vysledek
CREATE TABLE vysledek (
    id INTEGER NOT NULL PRIMARY KEY 
    ,nazev VARCHAR(50) NOT NULL UNIQUE
);
INSERT INTO vysledek (id,nazev) VALUE (-1,'chyba');
INSERT INTO vysledek (id,nazev) VALUE (1,'správně');


-- Tabulka: Výsledky
CREATE TABLE vysledky (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
    ,prevod_id INTEGER REFERENCES prevody(id)
    ,uzivatel_id INTEGER REFERENCES uzivatele(id)
    ,priklad VARCHAR(100) NOT NULL 
    ,vysledek_id INTEGER REFERENCES vysledek(id) 
);     
