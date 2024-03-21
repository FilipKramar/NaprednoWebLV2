<?php

// Postavke za povezivanje na bazu podataka
$servername = "localhost";
$username = "root";
$password = "";
$database = "radovi";

// Funkcija za stvaranje backup-a baze podataka
function createDatabaseBackup($servername, $username, $password, $database) {
    // Povezivanje na bazu podataka
    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Dohvaćanje imena svih tablica u bazi podataka
    $result = $conn->query("SHOW TABLES");
    $tables = array();
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    // Otvaranje datoteke za pisanje
    $backup_file = 'backup.sql';
    $file_handle = fopen($backup_file, 'w');

    // Iteracija kroz sve tablice i dohvaćanje podataka
    foreach ($tables as $table) {
        $result = $conn->query("SELECT * FROM $table");
        $num_fields = $result->field_count;

        // Zapisivanje CREATE TABLE naredbe u .sql datoteku
        $create_table_query = $conn->query("SHOW CREATE TABLE $table");
        $create_table_row = $create_table_query->fetch_row();
        fwrite($file_handle, $create_table_row[1] . ";\n");

        // Zapisivanje INSERT naredbi u .sql datoteku
        while ($row = $result->fetch_assoc()) {
            fwrite($file_handle, "INSERT INTO $table (");
            $keys = array_keys($row);
            for ($i = 0; $i < $num_fields; $i++) {
                fwrite($file_handle, $keys[$i]);
                if ($i < ($num_fields - 1)) {
                    fwrite($file_handle, ", ");
                }
            }
            fwrite($file_handle, ") VALUES (");
            foreach ($row as $key => $value) {
                fwrite($file_handle, "'" . $conn->real_escape_string($value) . "'");
                if ($key !== end($keys)) {
                    fwrite($file_handle, ", ");
                }
            }
            fwrite($file_handle, ");\n");
        }
        fwrite($file_handle, "\n");
    }

    // Zatvaranje datoteke
    fclose($file_handle);

    echo "Backup successfully saved as $backup_file";

    // Zatvaranje veze s bazom podataka
    $conn->close();
}

// Pozivanje funkcije za stvaranje backup-a baze podataka
createDatabaseBackup($servername, $username, $password, $database);

?>
