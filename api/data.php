<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$range = isset($_GET['range']) ? intval($_GET['range']) : 30;

// Beispiel-API-Endpunkt (Influenza A oder SARS-CoV-2)
$apiUrl = "https://data.bs.ch/api/explore/v2.1/catalog/datasets/100187/records?limit" . $range;

// API-Daten holen
$apiResponse = @file_get_contents($apiUrl);

if ($apiResponse === FALSE) {
    echo json_encode(["error" => "Fehler beim Abrufen der API-Daten"]);
    exit;
}

$data = json_decode($apiResponse, true);

// Rohdaten prüfen
if (!isset($data['results'])) {
    echo json_encode(["error" => "Unerwartetes API-Format", "data" => $data]);
    exit;
}

// Werte extrahieren
$result = [];
foreach ($data['results'] as $row) {
    $datum = $row['datum'] ?? null;
    $viruswert = $row['7t_median_bs_bl'] ?? null;

    if ($datum && $viruswert !== null) {
        $risiko = "niedrig";
        if ($viruswert > 30) $risiko = "hoch";
        elseif ($viruswert > 15) $risiko = "mittel";

        $result[] = [
            "datum" => $datum,
            "viruswert" => $viruswert,
            "risiko_level" => $risiko
        ];
    }
}

echo json_encode(array_slice($result, -$range));
