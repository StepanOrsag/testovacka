<?php

declare(strict_types=1);

require __DIR__ . '/../../src/ParcelRepository.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $zoom = isset($_GET['zoom']) ? (int) $_GET['zoom'] : 0;
    $bbox = $_GET['bbox'] ?? null;

    if ($zoom < 17) {
        echo json_encode([
            'type' => 'FeatureCollection',
            'features' => [],
            'message' => 'Pro zobrazení parcel přibližte mapu'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if(!$bbox) {
        http_response_code(400);
        echo json_encode(['error' => 'Chybí parametr bbox'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $coords = array_map('floatval', explode(',', $bbox));
    if(count($coords) !== 4) {
        http_response_code(400);
        echo json_encode(['error' => 'Neplatný formát bbox'
        ], JSON_UNESCAPED_UNICODE,);
        exit;
    }

    [$minX, $minY, $maxX, $maxY] = $coords;

    $repository = new ParcelRepository();
    $geojson = $repository->getParcelsInBox($minX, $minY, $maxX, $maxY);

    echo $geojson;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Chyba serveru' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}