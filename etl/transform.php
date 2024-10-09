<?php

// Inkludiert das extract-Skript und speichert die extrahierten Daten in $data
$data = include('extract.php');

// Definiert eine Funktion zur Transformation der Daten
function transformData($data) {
    // Initialisiert ein leeres Array für die transformierten Daten
    $transformedData = [];

    // Iteriert über jede Station in den extrahierten Daten
    foreach ($data['stations'] as $station) {
        // Initialisiert Zähler für normale Fahrräder und E-Bikes
        $bikes = 0;
        $ebikes = 0;

        // Zählt die Anzahl der normalen Fahrräder und E-Bikes an jeder Station
        foreach ($station['vehicles'] as $vehicle) {
            if ($vehicle['type']['name'] == 'Bike') {
                $bikes++;
            } elseif ($vehicle['type']['name'] == 'E-Bike') {
                $ebikes++;
            }
        }

        // Erstellt ein neues Array mit den transformierten Daten für jede Station
        $transformedData[] = [
            'id' => $station['id'],
            'lat' => $station['latitude'],
            'lon' => $station['longitude'],
            'name' => $station['name'],
            'address' => $station['address'],
            'city' => $station['city'],
            'zip' => $station['zip'],
            'bikes' => $bikes,
            'ebikes' => $ebikes,
            'totalbikes' => $bikes + $ebikes,
            'weekday' => date('l'), // Fügt den aktuellen Wochentag hinzu
            'hour' => date('G'), // Fügt die aktuelle Stunde des Tages hinzu (0-23)
            'month' => date('m') // Fügt den aktuellen Monat des Jahres hinzu (01-12)
        ];
    }

    // Gibt die transformierten Daten zurück
    return $transformedData;
}

// Ruft die Transformationsfunktion auf und speichert das Ergebnis
$transformedData = transformData($data);

// Gibt die transformierten Daten zurück
return $transformedData;