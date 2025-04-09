<?php

class Database {
    private $pdo;

    // Конструктор: подключение к базе данных
    public function __construct($path) {
        try {
            $this->pdo = new PDO("sqlite:" . $path);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    // Выполнение SQL-запроса без возврата результата
    public function Execute($sql) {
        return $this->pdo->exec($sql);
    }

    // Выполнение SQL-запроса с возвратом результата
    public function Fetch($sql) {
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Создание новой записи в таблице
    public function Create($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($key) => ':' . $key, array_keys($data)));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $this->pdo->lastInsertId();
    }

    // Чтение записи по ID
    public function Read($table, $id) {
        $sql = "SELECT * FROM $table WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Обновление записи по ID
    public function Update($table, $id, $data) {
        $assignments = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
        $data['id'] = $id;

        $sql = "UPDATE $table SET $assignments WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    // Удаление записи по ID
    public function Delete($table, $id) {
        $sql = "DELETE FROM $table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Подсчет количества записей в таблице
    public function Count($table) {
        $sql = "SELECT COUNT(*) as count FROM $table";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
}

?>
