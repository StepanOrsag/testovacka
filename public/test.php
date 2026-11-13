<?php
require_once __DIR__ . '/../src/Database.php';

try {
    $db = Database::getConnection();
    $count = $db->query("SELECT count(*) FROM parcels")->fetchColumn();
    echo "<h1>Připojení k DB úspěšné!</h1>";
    echo "<p>V databázi je celkem {$count} parcel.</p>";
} catch (Exception $e) {
    echo "<h1>Chyba: " . $e->getMessage() . "</h1>";
}