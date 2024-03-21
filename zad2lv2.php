<?php
$target_dir = "uploads/"; // Direktorij gdje će se pohranjivati datoteke

// Provjerava se da li je metoda zahtjeva POST i da li postoji datoteka koja se prenosi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $target_file = $target_dir . basename($_FILES["file"]["name"]); // Putanja do ciljne datoteke
    $uploadOk = 1; // Postavljanje oznake za uspješan upload na 1
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // Dobivanje ekstenzije datoteke

    // Provjera da li je datoteka valjana slika ili dokument
    if (!in_array($imageFileType, array("pdf", "jpeg", "jpg", "png"))) {
        echo "Žao nam je, dozvoljeni su samo PDF, JPEG, JPG i PNG formati datoteka.";
        $uploadOk = 0; // Postavljanje oznake za uspješan upload na 0
    }

    // Provjera da li je došlo do greške tijekom uploada
    if ($uploadOk == 0) {
        echo "Žao nam je, vaša datoteka nije prenesena.";
    } else {
        // Generiranje nasumičnog inicijalizacijskog vektora (IV)
        $iv = openssl_random_pseudo_bytes(16);

        // Enkripcija datoteke koristeći OpenSSL
        $file_content = file_get_contents($_FILES["file"]["tmp_name"]); // Čitanje sadržaja datoteke
        $encrypted_content = openssl_encrypt($file_content, 'aes-256-cbc', 'moja_tajna_kljuc', 0, $iv); // Enkripcija sadržaja

        // Pohrana enkriptiranog sadržaja u datoteku
        file_put_contents($target_file . '.enc', $encrypted_content);

        echo "Datoteka " . htmlspecialchars(basename($_FILES["file"]["name"])) . " je prenesena i enkriptirana. <br><br>";
    }
} else {
    // Generiranje nasumičnog inicijalizacijskog vektora (IV)
    $iv = openssl_random_pseudo_bytes(16);
    include 'zad2lv2.html'; // Uključivanje HTML obrasca za prijenos datoteka
}

// Dohvaćanje svih enkriptiranih datoteka
$files = glob($target_dir . '*.enc');

// Dekriptiranje i prikazivanje linkova za preuzimanje
foreach ($files as $file) {
    $filename = basename($file, '.enc');
    $decrypted_file = $target_dir . $filename;
    $decrypted_content = openssl_decrypt(file_get_contents($file), 'aes-256-cbc', 'moja_tajna_kljuc', 0, $iv); // Korištenje istog IV-a kao kod enkripcije
    file_put_contents($decrypted_file, $decrypted_content);

    // Prikazivanje linka za preuzimanje
    echo "<a href='$decrypted_file' download>$filename</a><br>";
}
?>
