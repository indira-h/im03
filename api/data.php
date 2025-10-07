<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 🔗 Verbindung zur Datenbank
require_once __DIR__ . '/../config/config.php';

// 🕒 Parameter: Zeitraum in Tagen (Standard: 30)
$range = isset($_GET['range']) ? intval($_GET['range']) : 30;

try {
    // 📊 SQL-Abfrage: letzte X Tage abrufen
    $stmt = $pdo->prepare("
        SELECT datum, viruswert, risiko_level
        FROM virus_data
        WHERE datum >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
        ORDER BY datum ASC
    ");
    $stmt->execute(['days' => $range]);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 🔄 Wenn keine Daten gefunden wurden, Dummy-Daten als Fallback
    if (empty($data)) {
        $data = [
            ["datum" => date('Y-m-d', strtotime('-6 days')), "viruswert" => 5, "risiko_level" => "niedrig"],
            ["datum" => date('Y-m-d', strtotime('-5 days')), "viruswert" => 9, "risiko_level" => "mittel"],
            ["datum" => date('Y-m-d', strtotime('-4 days')), "viruswert" => 12, "risiko_level" => "hoch"]
        ];
    }

    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed', 'details' => $e->getMessage()]);
    exit;
}
?>
