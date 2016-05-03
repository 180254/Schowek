<?php
$config = require 'config.php';

$file = $config['store_dir'] . '/' . $_GET['file'];

header('Content-Type: text/html; charset=utf-8');

if (!isset($_POST['password'])) {
    exit('Brak podanego hasła. Proszę pobierać pliki z użyciem formularza, nie klikać także "otwórz w nowej karcie".');
}
if ($_POST['password'] !== $config['password_download']) {
    exit('Podano nieprawidłowe hasło.');
}
if (!is_dir($config['store_dir'])) {
    exit('Błąd konfiguracji store_dir.');
}
if (!is_readable($config['store_dir'])) {
    exit('Błąd uprawnień odczytu store_dir.');
}
if (!file_exists($file)) {
    exit('Brak pliku docelowego.');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file));
readfile($file);