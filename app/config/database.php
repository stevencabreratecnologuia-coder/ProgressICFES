<?php

declare(strict_types=1);

class Database
{
    private static string $host = "localhost";
    private static string $db_name = "progressicfes";
    private static string $username = "root";
    private static string $password = "";

    public static function connect(): PDO
    {
        try {
            $pdo = new PDO(
                "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4",
                self::$username,
                self::$password
            );

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
