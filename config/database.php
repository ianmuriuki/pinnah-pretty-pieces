<?php
/*
 * Database connection for Pinnah's Pretty Pieces
 * Uses PDO for security and portability. Include in API and pages as needed.
 */

require_once __DIR__ . '/config.php'; // Load constants

class Database {
    private static $instance = null;
    public $pdo;
    private $host = DB_HOST;
    private $dbname = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;

    // Singleton: Get one connection instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4;unix_socket=/opt/lampp/var/mysql/mysql.sock";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Simple query wrapper (for quick selects; use prepared for inserts/updates)
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Get last insert ID
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    // Begin/Commit/Rollback for transactions (e.g., orders)
    public function beginTransaction() { return $this->pdo->beginTransaction(); }
    public function commit() { return $this->pdo->commit(); }
    public function rollback() { return $this->pdo->rollback(); }
}

// Usage example: $db = Database::getInstance(); $users = $db->query("SELECT * FROM users");
?>