<?php
require 'lib.TemplateEngine.php';
require 'lib.functions.php';
$config = require 'config.php';

define('BASE_NICE_MSG', 'OK.');
define('BASE_ERROR_MSG', 'Błąd!');
$niceMsg = '';
$errorMsg = '';

// var_dump($_POST);

if (!is_dir($config['store_dir'])) {
    $errorMsg = 'Folder docelowy store_dir (' . $config['store_dir'] . ') nie istnieje.';

} else if (!is_readable($config['store_dir'])) {
    $errorMsg = 'Proszę sprawdzić uprawnienia odczytu do store_dir (' . $config['store_dir'] . ').';

} else if (!is_writeable($config['store_dir'])) {
    $errorMsg = 'Proszę sprawdzić uprawnienia zapisu do store_dir (' . $config['store_dir'] . ').';

} else if (!empty($_FILES['uploadedFile'])) {

    $uploadedFile = $_FILES['uploadedFile'];
    $targetFile = $config['store_dir'] . '/' . basename($uploadedFile['name']);

    if (empty($uploadedFile['name'])) {
        $errorMsg = 'Nie wskazano żadnego pliku.';

    } else if (!isset($_POST['password']) || $_POST['password'] !== $config['password_upload']) {
        $errorMsg = 'Podano nieprawidłowe hasło.';

    } else if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = 'Wystąpił problem z wysyłaniem (' . uploadCodeToMessage($uploadedFile['error']) . ').';

    } elseif ($uploadedFile['size'] > $config['max_file_size_in_bytes']) {
        $errorMsg = 'Przekroczono maksymalny rozmiar pliku (wysłano: ' . $uploadedFile['size'] . 'B, ' .
            'dozwolony: ' . $config['max_file_size_in_bytes'] . 'B).';

    } elseif (!in_array(pathinfo($targetFile, PATHINFO_EXTENSION), $config['allowed_ext'])) {
        $errorMsg = 'Wysłany plik ma niedozwolone rozszerzenie (' . pathinfo($targetFile, PATHINFO_EXTENSION) . ').';

    } elseif (file_exists($targetFile)) {
        $errorMsg = 'Plik o takiej nazwie już istnieje ("' . basename($uploadedFile['name']) . '").';

    } else {
        if (@move_uploaded_file($uploadedFile['tmp_name'], $targetFile)) {
            $niceMsg = 'Plik "' . basename($uploadedFile['name']) . '" o typie ' . $uploadedFile['type'] . ' został pomyślnie dodany.';
        } else {
            $errorMsg = 'Wystąpił nieznany błąd wysyłania. ';
        }
    }

} else if (isset($_POST['deleteh'])) {
    if (!isset($_POST['deleted']) || !is_array($_POST['deleted'])) {
        $errorMsg = 'Nie zaznaczono żadnych plików.';

    } else if (!isset($_POST['password']) || $_POST['password'] !== $config['password_delete']) {
        $errorMsg = 'Podano nieprawidłowe hasło.';

    } else {
        $deleted = array();
        $notDeleted = array();

        foreach ($_POST['deleted'] as $deletedFileName) {
            $targetFile = $config['store_dir'] . '/' . $deletedFileName;

            if (is_file($targetFile) && unlink($targetFile)) {
                $deleted[] = '"' . $deletedFileName . '"';
            } else {
                $notDeleted[] = '"' . $deletedFileName . '"';
            }
        }

        if (count($deleted)) {
            $niceMsg = 'Pomyślnie skasowano: ' . implode($deleted, ",") . '.';
        }

        if (count($notDeleted)) {
            $errorMsg = 'Wystapił błąd podczas kasowania: ' . implode($notDeleted, ",") . '.';
        }
    }

}

$tbody = '';
if ($dir = @opendir($config['store_dir'])) {
    while ($filename = readdir($dir)) {
        if (preg_match('/^\.*$/', $filename)) {
            continue;
        }

        $filepath = $config['store_dir'] . '/' . $filename;

        $tr = new Template('tmpl.tr.html');
        $tr->add('ext', pathinfo($filepath, PATHINFO_EXTENSION));
        $tr->add('filename', $filename);
        $tr->add('filenameurl', urlencode($filename));
        $tr->add('date', date('Y-m-d', filectime($filepath)));
        $tr->add('size', filesize($filepath));
        $tbody .= $tr->execute();
    }
    closedir($dir);
}

if (strlen($niceMsg)) {
    $niceMsg = BASE_NICE_MSG . ' ' . $niceMsg;
}
if (strlen($errorMsg)) {
    $errorMsg = BASE_ERROR_MSG . ' ' . $errorMsg;
}

$index = new Template('tmpl.index.html');
$index->add('niceMsg', $niceMsg);
$index->add('errorMsg', $errorMsg);
$index->add('tbody', $tbody);
echo $index->execute();