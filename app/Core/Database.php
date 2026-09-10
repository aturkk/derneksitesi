<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;
use RuntimeException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function init(array $config): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            (int) ($config['port'] ?? 3306),
            $config['database'],
            $config['charset'] ?? 'utf8mb4'
        );
        self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            throw new RuntimeException('Veritabanı bağlantısı kurulmadı.');
        }
        return self::$pdo;
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function fetchColumn(string $sql, array $params = []): mixed
    {
        return self::run($sql, $params)->fetchColumn();
    }

    public static function insert(string $table, array $data): int
    {
        $cols = array_keys($data);
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', array_map(static fn ($c) => "`$c`", $cols)),
            implode(', ', array_fill(0, count($cols), '?'))
        );
        self::run($sql, array_values($data));
        return (int) self::pdo()->lastInsertId();
    }

    public static function update(string $table, array $data, array $where): int
    {
        $set = implode(', ', array_map(static fn ($c) => "`$c` = ?", array_keys($data)));
        $cond = implode(' AND ', array_map(static fn ($c) => "`$c` = ?", array_keys($where)));
        return self::run(
            "UPDATE `$table` SET $set WHERE $cond",
            array_merge(array_values($data), array_values($where))
        )->rowCount();
    }

    public static function delete(string $table, array $where): int
    {
        $cond = implode(' AND ', array_map(static fn ($c) => "`$c` = ?", array_keys($where)));
        return self::run("DELETE FROM `$table` WHERE $cond", array_values($where))->rowCount();
    }
}
