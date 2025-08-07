<?php

class DB {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO("sqlite:" . ROOT_PATH . 'database/database.sqlite');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function createTables() {
        $commands = [
            '
            CREATE TABLE IF NOT EXISTS roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL UNIQUE
            )
            ',
            '
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role_id INTEGER,
                FOREIGN KEY (role_id) REFERENCES roles(id)
            )
            ',
            '
            CREATE TABLE IF NOT EXISTS leave_types (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL UNIQUE
            )
            ',
            '
            CREATE TABLE IF NOT EXISTS leave_requests (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                leave_type_id INTEGER NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                reason TEXT,
                status VARCHAR(255) NOT NULL DEFAULT \'pending\',
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (leave_type_id) REFERENCES leave_types(id)
            )
            ',
            '
            CREATE TABLE IF NOT EXISTS attendance (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                check_in_time DATETIME NOT NULL,
                check_out_time DATETIME,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
            ',
            '
            CREATE TABLE IF NOT EXISTS payroll (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                month INTEGER NOT NULL,
                year INTEGER NOT NULL,
                salary DECIMAL(10, 2) NOT NULL,
                deductions DECIMAL(10, 2) NOT NULL,
                net_pay DECIMAL(10, 2) NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
            '
        ];

        foreach ($commands as $command) {
            $this->pdo->exec($command);
        }

        // Insert default roles
        $sql = "INSERT OR IGNORE INTO roles (name) 
                SELECT 'admin' 
                UNION ALL 
                SELECT 'user'";
        $this->pdo->exec($sql);
    }
}

$db = DB::getInstance();
$db->createTables();
