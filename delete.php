<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/classes/BookRepository.php';

$deleted = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repo = new BookRepository($pdo);
    $deleted = $repo->deleteBook((int) ($_POST['id'] ?? 0));
}

header('Location: index.php?deleted=' . $deleted);
exit;