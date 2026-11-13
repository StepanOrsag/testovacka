# Jak aplikaci spustit
  - potřeba mít nainstalovaný docker
  - v kořenovém adresáři projektu spustit: 
    - docker compose up -d web db
  - webová apka by měla být dostupná na -> http://localhost:8080

# Obnova importu dat
  - data jsou stáhnutá v data/
  - pokud je potřeba je znovu naplnit:
    - docker compose run --rm importer

# Použitý stack
  - Backend: čisté PHP 8.3
  - Databáze: PostreSQL 15 s rozšířením PostGIS 3.3
  - Frontend: Leaflet.js
  - infra: Docker Compose

# Struktura projektu 
  - data/ #stažená data ČÚZK a import skript import.sh
  - public/ #root pro webserver
    - css/style.css #ui
    - js/app.js #Leaflet mapa, BBOX logika, výběr parcel
    - api/parcels.php #REST API vracející geoJSON
    - index.html #vstupní stránka
  - src/ #aplikační backend logika
    - Database.php # PDO připojení k PostgreSQL
    - ParcelRepository.php #dotazy
  - Dockerfile #definice php kontejneru a pdo_pgsql
  - docker-compose.yml #konfigurace docker služeb
  - README.md #dokumentace