Pro instalaci je potřeba:

- Nainstalovat EasyPHP DevServer 17 (https://www.easyphp.org/)
- Spustit Webový server
- Spustit DB server
- V PhpMYAdmin vytvořit novou databázi "jednotky"
- Nainstalujte si Composer (https://doc.nette.org/cs/3.0/composer Composer.Setup.exe)
- Spustit příkazovou řádku v main adrešáři projektu
- Spustit sestavení projektu po "composer i"
- V root adresáři se vytvoření vendor/ kam se natáhne Nette, Latte atd...
- Nastavení DB v App/Config/local.neon
  - dsn: 'mysql:host=127.0.0.1;dbname=jednotky'
	  user: root
	  password:

<b>Struktura</b>
- app/
	config/ - nastavení DB. Do common je přidán nově authenticator: App\MyAuthenticator pro přihlášení skrze MD5
	presenters/ - složí pro CONTROLLER a práci s daty	
		templates/ - jednotlivé stránky a základný @layout pro menu, VIEW
- sql/	- skript pro vytvoření jednotlivých tabulek
- www/	- index.php, základ pro aplikaci
	css/ - defaultní styly
	
