Pro instalaci je potřeba:

- Nainstalovat EasyPHP DevServer 17 (https://www.easyphp.org/)
- Spustit Webový server (http://127.0.0.1/)
- Spustit DB server (http://127.0.0.1/eds-modules/phpmyadmin470x200525161419/)
- V PhpMYAdmin vytvořit novou databázi "jednotky", nastavit porovnávání na utf8_czech_ci
- Spustit skript pro vytvoření databázové struktury v této databázi. (sql\main.sql), který naplní základní číselníky, vytvoří admin uživatele a vyplní již testovací převody (pro ukázku na 2 veličinách)
- Stáhnout si Composer (https://doc.nette.org/cs/3.0/composer soubor Composer.Setup.exe) a umístit ho do kořenového adresáře projektu
- Spustit příkazovou řádku v main adrešáři projektu
- Spustit sestavení projektu po "composer i"
- V root adresáři se vytvoření vendor/ kam se natáhne Nette, Latte atd...
- Nastavení databáze upravit v souboru App/Config/local.neon
  - dsn: 'mysql:host=127.0.0.1;dbname=jednotky'
	  user: root
	  password:

<b>Struktura</b>
- app/
- app/config/ - nastavení DB. Do common je přidán nově authenticator: App\MyAuthenticator pro přihlášení skrze MD5
- app/presenters/ - složí pro CONTROLLER a práci s daty	
- app/presenters/templates/ - jednotlivé stránky a základný @layout pro menu, výstup 
- sql/	- skript pro vytvoření jednotlivých tabulek
- www/	- index.php, základ pro aplikaci
- www/css/ - defaultní styly (pro tuto aplikaci pouze minimální)
