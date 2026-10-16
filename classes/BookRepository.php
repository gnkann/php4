<?php

class BookRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(): array {
        return $this->pdo->query('SELECT * FROM books ORDER BY id')->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function findByAuthor(string $author): array {
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE author = :author');
        $stmt->execute([':author' => $author]);
        return $stmt->fetchAll();
    }

    public function listAvailable(): array {
        return $this->pdo->query('SELECT * FROM books WHERE is_available = 1')->fetchAll();
    }

    public function addBook(string $title, string $author, int $year, string $isbn, bool $isAvailable): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO books (title, author, year, isbn, is_available)
             VALUES (:title, :author, :year, :isbn, :is_available)'
        );
        $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':year' => $year,
            ':isbn' => $isbn,
            ':is_available' => $isAvailable ? 1 : 0,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function updateBook(int $id, string $title, string $author, int $year, string $isbn, bool $isAvailable): int {
        $stmt = $this->pdo->prepare(
            'UPDATE books
             SET title = :title, author = :author, year = :year, isbn = :isbn, is_available = :is_available
             WHERE id = :id'
        );
        $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':year' => $year,
            ':isbn' => $isbn,
            ':is_available' => $isAvailable ? 1 : 0,
            ':id' => $id,
        ]);
        return $stmt->rowCount();
    }

    public function deleteBook(int $id): int {
        $stmt = $this->pdo->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }
}