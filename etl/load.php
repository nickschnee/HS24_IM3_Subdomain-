<?php

// Inkludiert die Konfigurationsdatei mit Datenbankverbindungsinformationen
require_once 'config.php';

// Inkludiert das transform-Skript und speichert die transformierten Daten
$transformedData = include('transform.php');

try {
    // Erstellt eine neue PDO-Instanz für die Datenbankverbindung
    $pdo = new PDO($dsn, $username, $password, $options);

    // Bereitet SQL-Statements vor
    // Statement zum Überprüfen, ob eine Station bereits existiert
    $stmtCheckStation = $pdo->prepare("SELECT id FROM stations WHERE id = :id");
    // Statement zum Einfügen einer neuen Station
    $stmtInsertStation = $pdo->prepare("INSERT INTO stations (id, lat, lon, name, address, city, zip) 
                                        VALUES (:id, :lat, :lon, :name, :address, :city, :zip)");
    // Statement zum Aktualisieren einer bestehenden Station
    $stmtUpdateStation = $pdo->prepare("UPDATE stations SET lat = :lat, lon = :lon, name = :name, 
                                        address = :address, city = :city, zip = :zip WHERE id = :id");
    // Statement zum Einfügen von Fahrzeugdaten
    $stmtInsertVehicle = $pdo->prepare("INSERT INTO vehicles (station_id, bikes, ebikes, totalbikes, weekday, hour, month) 
                                        VALUES (:station_id, :bikes, :ebikes, :totalbikes, :weekday, :hour, :month)");

    // Startet eine Transaktion
    $pdo->beginTransaction();

    // Iteriert über jede Station in den transformierten Daten
    foreach ($transformedData as $station) {
        // Führt die vorbereitete Abfrage aus, um zu prüfen, ob die Station existiert
        $stmtCheckStation->execute(['id' => $station['id']]);

        // Holt das Ergebnis der Abfrage
        $exists = $stmtCheckStation->fetch(PDO::FETCH_ASSOC);

        if (!$exists) {
            // Fügt eine neue Station ein, wenn sie noch nicht existiert
            $stmtInsertStation->execute([
                'id' => $station['id'],
                'lat' => $station['lat'],
                'lon' => $station['lon'],
                'name' => $station['name'],
                'address' => $station['address'],
                'city' => $station['city'],
                'zip' => $station['zip']
            ]);
        } else {
            // Aktualisiert eine bestehende Station
            $stmtUpdateStation->execute([
                'id' => $station['id'],
                'lat' => $station['lat'],
                'lon' => $station['lon'],
                'name' => $station['name'],
                'address' => $station['address'],
                'city' => $station['city'],
                'zip' => $station['zip']
            ]);
        }

        // Fügt Fahrzeugdaten ein
        $stmtInsertVehicle->execute([
            'station_id' => $station['id'],
            'bikes' => $station['bikes'],
            'ebikes' => $station['ebikes'],
            'totalbikes' => $station['totalbikes'],
            'weekday' => $station['weekday'],
            'hour' => $station['hour'],
            'month' => $station['month']
        ]);
    }

    // Bestätigt die Transaktion
    $pdo->commit();

    echo "Daten erfolgreich geladen!";
    
} catch (PDOException $e) {
    // Macht die Transaktion rückgängig, wenn ein Fehler aufgetreten ist
    $pdo->rollBack();
    echo "Fehler: " . $e->getMessage();
}
