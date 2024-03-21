<?php
// Funkcija za parsiranje XML datoteke i izvlačenje podataka o osobama
function parse_xml($filename) {
    // Učitavanje XML datoteke
    $xml = simplexml_load_file($filename);
    if ($xml === false) {
        die('Greška pri učitavanju XML datoteke.');
    }

    $profiles = array();

    // Iteriranje kroz svaki zapis o osobi u XML-u
    foreach ($xml->record as $person) {
        // Izvlačenje podataka o osobi
        $id = (string)$person->id;
        $ime = (string)$person->ime;
        $prezime = (string)$person->prezime;
        $email = (string)$person->email;
        $spol = (string)$person->spol;
        $slika = (string)$person->slika;
        $zivotopis = (string)$person->zivotopis;

        // Stvaranje profila osobe i dodavanje u listu profila
        $profile = array(
            'id' => $id,
            'ime' => $ime,
            'prezime' => $prezime,
            'email' => $email,
            'spol' => $spol,
            'slika' => $slika,
            'zivotopis' => $zivotopis
        );
        $profiles[] = $profile;
    }

    return $profiles;
}

// Funkcija za prikazivanje profila osobe
function display_profile($profile) {
    echo "Ime: " . $profile['ime'] . "<br>";
    echo "Prezime: " . $profile['prezime'] . "<br>";
    echo "Email: " . $profile['email'] . "\n<br>";
    echo "Životopis: " . $profile['zivotopis'] . "<br>";
    echo "Slika: " . $profile['slika'] . "<br>";
    echo "\n<br>";
}


// Glavni dio skripte
$filename = 'LV2.xml';
$profiles = parse_xml($filename);

foreach ($profiles as $profile) {
    display_profile($profile);
}
?>
