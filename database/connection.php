<?php

class DatabaseConnection
{
    private $pdo;

    private function __construct()
    {
        $databaseConfiguration = include "../config/database.php";

        $this->pdo = new PDO(
            "mysql:host={$databaseConfiguration['host']};dbname={$databaseConfiguration['database']}",
            $databaseConfiguration['username'],
            $databaseConfiguration['password']
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private static $instance = null;

    public static function getInstance()
    {
        if (DatabaseConnection::$instance == null) {
            DatabaseConnection::$instance = true;
        }

        return DatabaseConnection::$instance;
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function query($query, $attrs)
    {
        $stmt = $this->pdo->prepare($query);

        $stmt->execute($attrs);

        return $stmt->fetchAll();
    }

    public function execute($query, $attrs)
    {
        $stmt = $this->pdo->prepare($query);

        $stmt->execute($attrs);

        return $stmt->rowCount();
    }

    public function rollBack()
    {
        $this->pdo->rollBack();
    }

    public function commit()
    {
        $this->pdo->commit();
    }
}