#!/bin/bash
set -e

echo "Čekám na dostupnost PostgreSQL..."
until ogrinfo "PG:host=db port=5432 dbname=parcel_db user=viagem_user password=secretpassword" > /dev/null 2>&1; do
    sleep 2
done

for f in /data/*.xml.zip; do
    [ -e "$f" ] || continue
    echo "Importuji $f..."
    ogr2ogr -f PostgreSQL "PG:host=db port=5432 dbname=parcel_db user=viagem_user password=secretpassword" "/vsizip/$f" \
        -nln parcels -append \
        -sql "SELECT Id AS id, KmenoveCislo AS kmenove_cislo, PododdeleniCisla AS poddeleni_cisla, VymeraParcely AS vymera, DruhPozemkuKod AS druh_pozemku_kod, ZpusobyVyuzitiPozemku AS zpusob_vyuziti_kod, KatastralniUzemiKod AS ku_kod, OriginalniHranice FROM Parcely WHERE OriginalniHranice IS NOT NULL" \
        -nlt PROMOTE_TO_MULTI -lco GEOMETRY_NAME=geom -s_srs EPSG:5514 -t_srs EPSG:4326

    ogr2ogr -f PostgreSQL "PG:host=db port=5432 dbname=parcel_db user=viagem_user password=secretpassword" "/vsizip/$f" \
        -nln cadastral_areas -append -nlt NONE \
        -sql "SELECT Kod AS kod, Nazev AS nazev FROM KatastralniUzemi"
done

echo "Import dokončen."