Vlastní PostGIS místo live:

* živé dotazování má vysokou latenci, pro tolik parcel by aplikace zamrzla. Proto jsem zvolil předem stažená data uložená v PostGIS



Strategie plynulosti:

* vykreslování 300 tisíc parcel na jedné obrazovce by vedlo k nečitelnosti a k neplynulosti aplikace, proto jsem zvolil GIS vrstvení:

  * Když je zoom menší než 17, tak se parcely netahají, mapa zobrazuje jen čárkovanou hranici okresu Jičín. Kliknutím kdekoliv na okres se mapa přiblíží na zoom 17, aby se zobrazily dané parcely
  * Detail: Backend pošle jen parcely, které jsou zrovna viditelné na obrazovce



Vykreslování přes Canvas zvýšilo plynulost aplikace



Generování GeoJSONu přímo v PostgreSQL

* Místo načítání do paměti PHP a volání json\_encode jsem využil funkce json\_build\_object. Databáze vrátí hotový JSON string, což drží paměť na minimu



Aktivní výběr parcely

* aktivní parcela se obarví červeně a otevře detail v pravém horním rohu. Při kliknutí jinam nebo na křížek u detailu se aplikace vrátí do původního stavu



Co mě překvapilo:

* VFR soubory obsahují jak definiční body, tak polygonové hranice. Při importu jsem musel explicitně vybrat polygonovou vrstvu a ohlídat souřadnicový přenos ze S-JTSK do WGS 84



Co bych s více časem přidal / řešil jinak:

* Vyhledávání parcel podle čísla
* Http caching: doplnění cache hlaviček na API endpoint pro opakované dotazy na stejný výřez mapy

