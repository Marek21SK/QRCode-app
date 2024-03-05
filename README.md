# QR Code app

QR Code app je webová aplikácia vytvorená v PHP s použítim HTML - Bootstrap 5 a MySQL databázy, ktorá umožňuje pridanie IBAN (*International Bank Account Number*) do databázy, načítanie tohto IBAN-u a následne generovanie QR kódu pre platbu mobilom.

## Inštalácia 

1.  - Stiahnutie a nainštalovanie WAMP server.
    - Spustenie WAMP server.

2.  - Stiahnutie a nainštalovanie MySQL Workbench.
    - Pripojenie sa k lokálnemu MySQL serveru.
    - Vytvorenie databázy s názvom 'qrcodoe_dev'.
    - Štruktúra databázy sa nachádza v priečinku "db_structure", pre jednoduchšie nastavenie tejto databázy do MySQL Workbench.

3.  - Nakopírovanie obsahu repozitára do priečinku 'www' v adresári WAMP.

4.  - Upravenie súborov 'database.php' a 'data.json' pre pripojenie MySQL databázy podľa vašich nastavení.

## Použitie 

1. Otvorenie aplikácie vo webovom prehliadači (http://localhost/qrcode-app/app/index.php).
2. Vytvorenie si používateľského konta cez Registráciu a následné prihlásenie sa do aplikácie cez Prihlásenie.
3. Použitie formuláru na pridanie IBAN-u do databázy.
4. Prehľad vložených IBAN-ov.
5. Použitie formuláru na načítanie IBAN-u z databázy a generovanie QR kódu pre platbu mobilom na základe zadaných údajov o platbe.
6. Prehľad o všetkých vykonaných platbách + QR kód ku všetkým týmto platbám.

## Funkcie 

- Jednoduché pridávanie IBAN-ov do databázy.
- Prehľad všetkých IBAN-ov a platieb.
- Generovanie QR kódu pre platbu mobilom. 

## Závislosti

- WAMP server
- PHP 7.0 a vyššie
- MySQL databáza
- MySQL Workbench pre vytvorenie databázy
- HTML + Bootstrap 5
- jQuery a JavaScript
- Implementovanie API na generovanie QR kódov (https://www.freebysquare.sk/api)

## Licencia

[FOSS]