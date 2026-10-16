<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/classes/BookRepository.php';
require_once __DIR__ . '/lib/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

[$data, $errors] = validateBookInput($_POST);

if (!empty($errors)) {
    header('Location: index.php?error=1');
    exit;
}

$repo = new BookRepository($pdo);
$newId = $repo->addBook(
    $data['title'],
    $data['author'],
    (int) $data['year'],
    $data['isbn'],
    $data['is_available']
);

header('Location: index.php?added=' . $newId);
exit;