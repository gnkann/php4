<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/classes/BookRepository.php';
require_once __DIR__ . '/lib/validation.php';

$repo = new BookRepository($pdo);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    [$data, $errors] = validateBookInput($_POST);

    if (empty($errors)) {
        $changed = $repo->updateBook(
            $id,
            $data['title'],
            $data['author'],
            (int) $data['year'],
            $data['isbn'],
            $data['is_available']
        );
        header('Location: index.php?updated=' . $changed);
        exit;
    }

    // Є помилки: показуємо форму знову з введеними значеннями
    $book = [
        'id' => $id,
        'title' => $data['title'],
        'author' => $data['author'],
        'year' => $data['year'],
        'isbn' => $data['isbn'],
        'is_available' => $data['is_available'] ? 1 : 0,
    ];
} else {
    $id = (int) ($_GET['id'] ?? 0);
    $book = $repo->findById($id);
    if ($book === null) {
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагувати книгу</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 40px auto; }
        label { display: block; margin-top: 8px; }
        input[type=text], input[type=number] { padding: 6px; width: 100%; box-sizing: border-box; }
        .error { color: #c0392b; }
    </style>
</head>
<body>
    <h1>Редагувати книгу</h1>

    <?php foreach ($errors as $error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>

    <form method="post" action="edit.php">
        <input type="hidden" name="id" value="<?= (int) $book['id'] ?>">
        <label>Назва <input type="text" name="title" required value="<?= htmlspecialchars($book['title']) ?>"></label>
        <label>Автор <input type="text" name="author" required value="<?= htmlspecialchars($book['author']) ?>"></label>
        <label>Рік <input type="number" name="year" min="1450" max="<?= date('Y') ?>" required value="<?= htmlspecialchars((string) $book['year']) ?>"></label>
        <label>ISBN <input type="text" name="isbn" pattern="\d{10}(\d{3})?" required value="<?= htmlspecialchars($book['isbn']) ?>"></label>
        <label><input type="checkbox" name="is_available" <?= $book['is_available'] ? 'checked' : '' ?>> Доступна</label>
        <button type="submit">Зберегти</button>
        <a href="index.php">Скасувати</a>
    </form>
</body>
</html>