# Geospatial Databases (SpatialDB)

## Project "My Big Fat Greek Truckjectories"

DEADLINE = 17.12.2015, 14 Uhr

1) Datenbank anlegen
- Datenbank erzeugen: fertig
- Schema erzeugen: fertig

2) Datenbank mit Daten befüllen
- Daten vorverarbeiten: fertig
- Daten einfügen: fertig

3) Webservice erstellen
- index.php: fertig
- trajectory.php: fertig
- staypoints.php: in Arbeit
- neue Idee für nächsten Tab!?: ausstehend

4) Refactoring: ausstehend

---

## Sonstiges
Im folgenden sind Links gelistet, die nützlich erscheinen:

### imposm3 import
- ./imposm3 import -connection postgis://USER:PASSWORD@SERVER/DATABASE -mapping mapping.json -read AREA_FILE.osm.pbf -write

### Postgresql using PHP
Tutorial: http://www.tutorialspoint.com/postgresql/postgresql_php.htm

### Leaflet Map Visualization (JavaScript)
- http://leafletjs.com/

### Stay Point Detection Algorithm
- http://www.cs.columbia.edu/~yeyang/pattern.pdf

### Ähnliches Projekt (Stay Point Detection)
- https://github.com/PseudoAj/AjGeoStay

---

### Alle nächsten Tankstellen: (Distanz in Metern)

select osm.name, ST_Distance(ST_MakePoint(ST_Y(ST_Transform(osm.geometry,4326)),ST_X(ST_Transform(osm.geometry,4326)),4326),ST_MakePoint(ST_X(ST_Transform(p.lonlat,4326)),ST_Y(ST_Transform(p.lonlat,4326)),4326),True)
from "osm_amenities" osm, "Point" p 
where osm.type = 'fuel' 
  and osm.name <> '' 
  and ST_Distance(ST_MakePoint(ST_Y(ST_Transform(osm.geometry,4326)),ST_X(ST_Transform(osm.geometry,4326)),4326),ST_MakePoint(ST_X(ST_Transform(p.lonlat,4326)),ST_Y(ST_Transform(p.lonlat,4326)),4326),True) < 5000
  and p.id = 1
order by ST_Distance(ST_MakePoint(ST_Y(ST_Transform(osm.geometry,4326)),ST_X(ST_Transform(osm.geometry,4326)),4326),ST_MakePoint(ST_X(ST_Transform(p.lonlat,4326)),ST_Y(ST_Transform(p.lonlat,4326)),4326),True)

---

Letztes Update 02.02.2016 - 01:47 Uhr
