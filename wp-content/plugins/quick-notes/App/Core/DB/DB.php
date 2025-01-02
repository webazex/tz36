<?php

namespace WBZXTDL\App\Core\DB;

use WBZXTDL\App\Core\Exception\Exception as Exception;
use WBZXTDL\App\Core\Logger\Logger as Logger;

class DB
{
    private static $allowedTables = [];
    private static $pdo = null; // Свойство для PDO
    private static $logger = null; // Свойство для логгера
    private static $wpdb = null;  // Свойство для $wpdb

    // Метод для инициализации $wpdb
    public static function initializeWpdb($wpdb): void
    {
        self::$wpdb = $wpdb;
    }

    public static function init(\PDO $pdo, $logger): void
    {
        global $wpdb;
        self::initializeWpdb($wpdb);
        self::initializeLogger($logger);
        self::initializePDO($pdo);
        self::initializeAllowedTables();
    }

    public static function initializeAllowedTables(): void
    {
        if (self::$wpdb === null) {
            throw new Exception(__('DB class is not properly initialized.', 'wbzx-tdl'));
        }

        self::$allowedTables = [
            self::$wpdb->prefix . 'todo_list', // Таблица для хранения тудушек
        ];
    }

    public static function initializePDO(\PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    public static function initializeLogger($logger): void
    {
        self::$logger = $logger;
    }
    public static function createTable(): void
    {
        if (self::$logger === null || self::$pdo === null || self::$wpdb === null) {
            throw new Exception(__('DB class is not properly initialized.', 'wbzx-tdl'));
        }

        $table_name = self::$wpdb->prefix . 'todo_list';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        edited_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id) REFERENCES " . self::$wpdb->prefix . "users(ID) ON DELETE CASCADE
    ) ENGINE=InnoDB;";

        try {
            self::$pdo->exec($sql);
            self::$logger->info(__("Table `$table_name` created successfully or already exists.", 'wbzx-tdl'));
        } catch (\Throwable $e) {
            self::$logger->error($e->getMessage());
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }


    protected static function isTableAllowed(string $table): bool
    {
        return in_array($table, self::$allowedTables, true);
    }

    public static function read(string $table, array $conditions = []): void
    {
        if (self::$pdo === null || self::$wpdb === null) {
            throw new Exception(__('DB class is not properly initialized.', 'wbzx-tdl'));
        }

        if (!self::isTableAllowed($table)) {
            throw new Exception(__("Unauthorized access to table `$table`.", 'wbzx-tdl'));
        }

        try {
            self::$pdo->beginTransaction();
            // Заготовка для чтения данных
            self::$pdo->commit();
        } catch (\Throwable $e) {
            self::$pdo->rollBack();
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    public static function update(string $table, array $data, array $conditions): void
    {
        if (self::$pdo === null || self::$wpdb === null) {
            throw new Exception(__('DB class is not properly initialized.', 'wbzx-tdl'));
        }

        if (!self::isTableAllowed($table)) {
            throw new Exception(__("Unauthorized access to table `$table`.", 'wbzx-tdl'));
        }

        try {
            self::$pdo->beginTransaction();
            // Заготовка для обновления данных
            self::$pdo->commit();
        } catch (\Throwable $e) {
            self::$pdo->rollBack();
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    public static function delete(string $table, array $conditions): void
    {
        if (self::$pdo === null || self::$wpdb === null) {
            throw new Exception(__('DB class is not properly initialized.', 'wbzx-tdl'));
        }

        if (!self::isTableAllowed($table)) {
            throw new Exception(__("Unauthorized access to table `$table`.", 'wbzx-tdl'));
        }

        try {
            self::$pdo->beginTransaction();
            // Заготовка для удаления данных
            self::$pdo->commit();
        } catch (\Throwable $e) {
            self::$pdo->rollBack();
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    public static function drop(string $table): void
    {
        if (self::$pdo === null || self::$wpdb === null) {
            throw new Exception(__('DB class is not properly initialized.', 'wbzx-tdl'));
        }

        if (!self::isTableAllowed($table)) {
            throw new Exception(__("Unauthorized access to table `$table`.", 'wbzx-tdl'));
        }

        try {
            self::$pdo->beginTransaction();
            // Заготовка для удаления таблицы
            self::$pdo->commit();
        } catch (\Throwable $e) {
            self::$pdo->rollBack();
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}





