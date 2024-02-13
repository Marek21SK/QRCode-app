# QR Code app

QR Code app je webová aplikácia vytvorená v PHP s použítim HTML - Bootstrap 5 a MySQL databázy, ktorá umožňuje pridanie IBAN (*International Bank Account Number*) do databázy, načítanie tohto IBAN-u a následne generovanie QR kódu pre platbu mobilom.

## Inštalácia 

1.  - Stiahnutie a nainštalovanie WAMP server.
    - Spustenie WAMP server.

2.  - Stiahnutie a nainštalovanie MySQL Workbench
    - Pripojenie sa k lokálnemu MySQL serveru.
    - Vytvorenie databázy s názvom 'qrcode_db'.

3.  - Nakopírovanie obsahu repozitára do priečinku 'www' v WAMP priečinku.

4.  - Upravenie 'config.php' súboru pre pripojenie MySQL databázy podľa vašich nastavení

## Použitie 

1. Otvorenie aplikácie vo webovom prehliadači (http://localhost/QRCode-app).
2. Použitie formuláru na pridanie IBAN-u do databázy.
3. Použitie formuláru na načítanie IBAN-u z databázy a generovanie QR kódu pre platbu mobilom.

## Funkcie 

- Jednoduché pridávanie IBAN-u do databázy.
- Generovanie QR kódu pre platbu mobilom. 

## Závislosti

- WAMP server
- PHP 7.0 a vyššie
- MySQL databáza
- MySQL Workbench pre vytvorenie databázy
- HTML + Bootstrap 5
