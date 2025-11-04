<?php
header('Content-Type: application/json; charset=utf-8');

// Verbindung Datenbank
require_once __DIR__ . '/../config/config.php';

try {
    // Range bestimmen (Standard: 30 Tage)
    $range = isset($_GET['range']) ? intval($_GET['range']) : 30;
    if ($range <= 0) $range = 30;

    // Letzte X Datensätze abrufen (egal vom Datum)
    $stmt = $pdo->prepare("
        SELECT datum, viruswert, risiko_level
        FROM virus_data
        ORDER BY datum DESC
        LIMIT :range
    ");
    $stmt->bindValue(':range', $range, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll();

    // Prüfen ob Daten vorhanden
    if (!$data || count($data) === 0) {
        echo json_encode(['message' => ' Keine Daten in der Datenbank gefunden.']);
        exit;
    }

    // aufsteigend sortieren 
    $data = array_reverse($data);

    // Ausgabe formatieren
    $formatted = array_map(function($row) {
        return [
            'datum' => $row['datum'],
            'viruswert' => (float)$row['viruswert'],
            'risiko_level' => $row['risiko_level']
        ];
    }, $data);

    // JSON-Ausgabe 
    echo json_encode($formatted, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    //  Fehlerbehandlung
    http_response_code(500);
    echo json_encode([
        'error' => 'Datenbankfehler',
        'details' => $e->getMessage()
    ]);
    exit;
}
?>
