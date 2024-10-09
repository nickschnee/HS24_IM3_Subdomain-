<?php

function fetchPublibikeData() {
    $url = "https://api.publibike.ch/v1/public/partner/stations";

    // Initialisiert eine cURL-Sitzung
    $ch = curl_init($url);

    // Setzt Optionen
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Führt die cURL-Sitzung aus und erhält den Inhalt
    $response = curl_exec($ch);

    // Schließt die cURL-Sitzung
    curl_close($ch);

    // Dekodiert die JSON-Antwort und gibt Daten zurück
    return json_decode($response, true);
}

// Gibt die Daten zurück, wenn dieses Skript eingebunden ist
return fetchPublibikeData();
?>