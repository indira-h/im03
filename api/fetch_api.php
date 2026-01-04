<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../config/config.php';

// URL API
$apiUrl = 'https://data.bs.ch/api/explore/v2.1/catalog/datasets/100187/records?limit=500&order_by=datum%20desc';

// cURL statt file_get_contents (da allow_url_fopen deaktiviert ist)
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Fehlerbehandlung API-Antwort
if ($httpCode !== 200 || !$response) {
    echo " API-Fehler: API konnte nicht geladen werden (HTTP $httpCode)";
    exit;
}

$data = json_decode($response, true);
if (!$data || !isset($data['results'])) {
    echo " API-Fehler: Antwort ungültig oder leer (HTTP $httpCode)";
    exit;
}

$records = $data['results'];
$count = 0;

// Datensätze nacheinander durchgehen
foreach ($records as $record) {
    $datum = $record['datum'] ?? null;
    $viruswert = $record['7_tagemedian_of_e_n1_n2_pro_tag_100_000_pers'] ?? null;

    if (!$datum || !$viruswert) continue;

    // Risiko berechnen basierend auf deinen Werten:
    // GERING: bis 0.5 Billionen = 5e11
    // MÄSSIG: bis 1.5 Billionen = 1.5e12
    // HOCH: ab 1.5 Billionen
    if ($viruswert < 5e11) {
        $risiko = 'gering';
    } elseif ($viruswert < 1.5e12) {
        $risiko = 'mäßig';
    } else {
        $risiko = 'hoch';
    }

    // Eintrag speichern oder aktualisieren
    $stmt = $pdo->prepare("
        INSERT INTO virus_data (datum, viruswert, risiko_level)
        VALUES (:datum, :viruswert, :risiko_level)
        ON DUPLICATE KEY UPDATE
            viruswert = VALUES(viruswert),
            risiko_level = VALUES(risiko_level)
    ");

    $stmt->execute([
        ':datum' => $datum,
        ':viruswert' => $viruswert,
        ':risiko_level' => $risiko
    ]);

    $count++;
}

echo "Import abgeschlossen: $count Einträge aktualisiert oder hinzugefügt.";
?>
