# Kočičí informační systém (backend)
> Daniel Dolejška (xdolej08@stud.fit.vutbr.cz)


## Instalace
1. `git clone https://github.com/dolejska-daniel/fit_vutbr-iis2018.git iis2018`
2. `cd iis2018 && composer install`
3. Aplikace vyžaduje konfigurační soubor databáze ve složce `config`. Název souboru odpovídá následujícímu řetězci `{APP_TARGET}.db.neon`. Proměnná `APP_TARGET` je definována v `index.php`.


## Princip zpracování požadavků
Systém zpracovává příchozí HTTP požadavky na `index.php`, používá **první** klíč z query požadavku jako identifikátor cílového endpointu. Název přeloží na názvy třídy a namespace ve kterém se nachází.

Method | URL                                                                      | Query                 | Target
-------|--------------------------------------------------------------------------|-----------------------|----------------------------
GET    | http://www.stud.fit.vutbr.cz/~xdolej08/IIS/backend/?/info                | ?/info                | GET\InfoEndpoint
POST   | http://www.stud.fit.vutbr.cz/~xdolej08/IIS/backend/?/user-mng/create&x=1 | ?/user-mng/create&x=1 | POST\UserMng\CreateEndpoint 

Pro přístup k některým endpointům budou muset být uživatelé nejen přihlášeni, ale mít také odpovídající systémová oprávnění. Více informací v sekci [Autentizace](#autentizace-uzivatelu) a [Autorizace uživatelů](#autorizace-uzivatelu).


## Autentizace uživatelů
_TBD_


## Autorizace uživatelů
_TBD_


## Endpointy
_TBD_
