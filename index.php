<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/classes/BookRepository.php';

$repo = new BookRepository($pdo);

$authorFilter = trim($_GET['author'] ?? '');
$onlyAvailable = isset($_GET['available']);

// Фільтри можна комбінувати
if ($authorFilter !== '') {
    $books = $repo->findByAuthor($authorFilter);
    if ($onlyAvailable) {
        $books = array_filter($books, fn(array $b) => (bool) $b['is_available']);
    }
} elseif ($onlyAvailable) {
    $books = $repo->listAvailable();
} else {
    $books = $repo->getAll();
}

// Повідомлення після дій (add.php, edit.php, delete.php)
$message = '';
if (isset($_GET['added'])) {
    $message = 'Книгу додано (id ' . (int) $_GET['added'] . ').';
} elseif (isset($_GET['updated'])) {
    $message = (int) $_GET['updated'] > 0 ? 'Зміни збережено.' : 'Змін не виявлено.';
} elseif (isset($_GET['deleted'])) {
    $message = (int) $_GET['deleted'] > 0 ? 'Книгу видалено.' : 'Книгу не знайдено.';
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Бібліотека F1 — база даних</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        label { display: block; margin-top: 8px; }
        input[type=text], input[type=number] { padding: 6px; width: 300px; }
        .error { color: #c0392b; }
        .message { color: #27ae60; font-weight: bold; }
        form.inline { display: inline; }
    </style>
</head>
<body>
    <h1>🏎️ Бібліотека книг про Формулу-1</h1>

    <?php if ($message !== ''): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <p class="error">Перевірте дані: усі поля обов'язкові, рік — від 1450 до <?= date('Y') ?>, ISBN — 10 або 13 цифр.</p>
    <?php endif; ?>

    <h2>Фільтри</h2>
    <form method="get" action="index.php">
        <input type="text" name="author" placeholder="Автор (точна назва)"
               value="<?= htmlspecialchars($authorFilter) ?>">
        <label><input type="checkbox" name="available" <?= $onlyAvailable ? 'checked' : '' ?>> Лише доступні</label>
        <button type="submit">Застосувати</button>
        <a href="index.php">Скинути</a>
    </form>

    <h2>Список книг (<?= count($books) ?>)</h2>
    <table>
        <tr>
            <th>ID</th><th>Назва</th><th>Автор</th><th>Рік</th><th>ISBN</th><th>Статус</th><th>Дії</th>
        </tr>
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?= (int) $book['id'] ?></td>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td><?= (int) $book['year'] ?></td>
                <td><?= htmlspecialchars($book['isbn']) ?></td>
                <td><?= $book['is_available'] ? 'Доступна' : 'Видана' ?></td>
                <td>
                    <a href="edit.php?id=<?= (int) $book['id'] ?>">Редагувати</a>
                    <form class="inline" method="post" action="delete.php"
                          onsubmit="return confirm('Видалити цю книгу?');">
                        <input type="hidden" name="id" value="<?= (int) $book['id'] ?>">
                        <button type="submit">Видалити</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Додати книгу</h2>
    <form method="post" action="add.php">
        <label>Назва <input type="text" name="title" required></label>
        <label>Автор <input type="text" name="author" required></label>
        <label>Рік <input type="number" name="year" min="1450" max="<?= date('Y') ?>" required></label>
        <label>ISBN <input type="text" name="isbn" pattern="\d{10}(\d{3})?" required></label>
        <label><input type="checkbox" name="is_available" checked> Доступна</label>
        <button type="submit">Додати</button>
    </form>
</body>
</html>