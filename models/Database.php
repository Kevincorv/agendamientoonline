<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        // Sincronizar timezone MySQL con PHP (America/Asuncion)
        $tzNombre = date_default_timezone_get();
        try {
            $this->pdo->exec("SET time_zone = '{$tzNombre}'");
        } catch (\Throwable) {
            $offset = (new DateTime('now', new DateTimeZone($tzNombre)))->format('P');
            $this->pdo->exec("SET time_zone = '{$offset}'");
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }
}
