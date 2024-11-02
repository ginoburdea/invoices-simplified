# Invoices simplified

Bun venit la proiectul "Facturi simplificate". Acesta aplicatie web ii va permite oricui sa creeze si sa de descarce facutri pentru clientii sai. Utilzatorii pot alege detaliile proprii de facturare deiferite la fiecare factura sau poate sa le preia de la cea anterioara.

## Instruciuni de utilizare

### 1. Cerinte de sistem

Pentru a utliza acest proiect, trebuie sa ai instalate pe computer:

1. [NodeJS si NPM](https://nodejs.org/en/download/package-manager)
1. [git](https://git-scm.com/downloads)
1. [PHP](https://www.php.net/downloads)
1. [MySQL](https://www.mysql.com/downloads/)

Recomandam [XAMPP](https://www.apachefriends.org/download.html) pentru a insta PHP si MySQL.

### 2. Descarcare proiect

Deschide un terminal / command prompt si tasteaza urmatoarele comenzi:

```sh
git clone https://github.com/ginoburdea/invoices-simplified.git
cd invoices-simplified
npm install
composer install
```

### 3. Variabile de environment

Copiaza fisierul `.env.example` si pune-i numele `.env` (Acest fisier va fi ignorat de git cand un commit este creat)

Deschide noul fisier si inlocuieste variabilele in functie de instructiunie din acesta.

Poti genera variabila `APP_KEY` folosind comanda de generare a cheii secrete din punctul 5.

### 4. Migrare baza de date

Ruleaza comanda de mai jos pentru a migra data de baze (la creare, baza de date este goala. Comenzile de mai jos creaza tabelele necesare).

```sh
php artisan migrate
```

### 5. Comenzi

In functie de obiectivul tau, foloseste una dintre urmatoarele comenzi:

```sh
# Genereaza cheia secreta pentru backend (genereaz-o o singura data!)
php artisan key:generate

# Goleste memoria cache (daca ai utilizat comenzile pentru modul de productie si acum doresti sa dezvolti)
php artisan optimize:clear

# Porneste frontend-ul si backend-ul
npm run dev

# Genereaza un "build" pentru frontend si optimizeaza backend-ul
npm run build
```

### 6. Link-uri

In modul de dezvoltare, aplicatia se poate accesa prin adresa http://localhost:8000. Linkurile aplicatiei se pot vedea folosind urmatoarea comanda:

```sh
php artisan route:list
```

## Instructiuni de dezvoltare

### Strategia de branching

Acest proiect utilizeaza strategia [Feature Branch](https://www.atlassian.com/git/tutorials/comparing-workflows/feature-branch-workflow):

Exista un branch `main` unde se afla codul functional si gata de trimis spre productie si cate un branch pentru fiecare task / user story.

1. Se creaza un branch pentru fiecare task
1. Se implementeaza solutia
1. Se deshide un Pull Request pe Github
1. Dupa review, Pull Request-ul se inchide prin strategia "Squash and Merge"
