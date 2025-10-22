<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/config.php';

// Bereich (z. B. 7 / 14 / 30 Tage) – Standard: 30
$range = isset($_GET['range']) ? intval($_GET['range']) : 30;

// Daten aus  eigener Datenbank lesen
try {
    $stmt = $pdo->prepare("
        SELECT datum, viruswert, risiko_level
        FROM virus_data
        ORDER BY datum DESC
        LIMIT :range
    ");
    $stmt->bindValue(':range', $range, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Älteste zuerst
    $rows = array_reverse($rows);

    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "DB-Fehler",
        "details" => $e->getMessage()
    ]);
}
