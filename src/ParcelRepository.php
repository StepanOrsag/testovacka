<?php

require_once __DIR__ . '/Database.php';

class ParcelRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function getParcelsInBox(float $minX, float $minY, float $maxX, float $maxY, int $limit = 3000): string
    {
        $sql = "
            SELECT json_build_object(
                'type', 'FeatureCollection',
                'features', COALESCE(json_agg(
                    json_build_object(
                        'type', 'Feature',
                        'id', p.id,
                        'geometry', ST_AsGeoJSON(p.geom)::json,
                        'properties', json_build_object(
                            'id', p.id,
                            'kmenove_cislo', p.kmenove_cislo,
                            'poddeleni_cisla', p.poddeleni_cisla,
                            'vymera', p.vymera,
                            'druh_pozemku_kod', p.druh_pozemku_kod,
                            'druh_pozemku_nazev', CASE p.druh_pozemku_kod
                                WHEN 2 THEN 'Orná půda'
                                WHEN 3 THEN 'Chmelnice'
                                WHEN 4 THEN 'Vinice'
                                WHEN 5 THEN 'Zahrada'
                                WHEN 6 THEN 'Ovocný sad'
                                WHEN 7 THEN 'Trvalý travní porost'
                                WHEN 10 THEN 'Lesní pozemek'
                                WHEN 11 THEN 'Vodní plocha'
                                WHEN 13 THEN 'Zastavěná plocha a nádvoří'
                                WHEN 14 THEN 'Ostatní plocha'
                                ELSE 'Neznámý druh'
                            END,
                            'zpusob_vyuziti_kod', p.zpusob_vyuziti_kod,
                            'ku_kod', p.ku_kod,
                            'ku_nazev', ca.nazev
                        )
                    )
                ), '[]'::json)
            ) AS geojson
            FROM parcels p
            LEFT JOIN (
                SELECT DISTINCT kod, nazev FROM cadastral_areas
            ) ca ON ca.kod = p.ku_kod
            WHERE ST_Intersects(p.geom, ST_MakeEnvelope(:minX, :minY, :maxX, :maxY, 4326))
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':minX', $minX);
        $stmt->bindValue(':minY', $minY);
        $stmt->bindValue(':maxX', $maxX);
        $stmt->bindValue(':maxY', $maxY);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchColumn();

        return $result ? : '{"type": "FeatureCollection", "features": []}';
    }
}