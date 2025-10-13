<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$range = isset($_GET['range']) ? intval($_GET['range']) : 30;

// API-URL für Dataset 100187 (SARS-CoV-2 Abwasser Basel)  
$apiUrl = "https://data.bs.ch/api/explore/v2.1/catalog/datasets/100187/records?sort=datum&limit=" . $range;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(["error" => "API-Fehler", "details" => curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

$data = json_decode($response, true);

if (!isset($data['results']) || !is_array($data['results'])) {
    echo json_encode(["error" => "Unerwartetes API-Format", "data" => $data], JSON_PRETTY_PRINT);
    exit;
}

$result = [];
foreach ($data['results'] as $row) {
    $datum = $row['datum'] ?? null;
    $viruswert = $row['7_tagemedian_of_e_n1_n2_pro_tag_100_000_pers'] ?? null;

    if ($datum !== null && $viruswert !== null) {
        $viruswert = floatval($viruswert);
        $risiko = "niedrig";
        if ($viruswert > 60) $risiko = "hoch";
        elseif ($viruswert > 30) $risiko = "mittel";

        $result[] = [
            "datum" => $datum,
            "viruswert" => $viruswert,
            "risiko_level" => $risiko
        ];
    }
}

// Älteste zuerst
echo json_encode(array_slice($result, 0, $range), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
