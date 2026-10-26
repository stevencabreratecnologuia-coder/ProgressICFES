<?php

require_once __DIR__ . '/../config/config.php';

class Database
{
    private static ?PDO $conexion = null;

    public static function obtenerConexion(): PDO
    {
        if (self::$conexion === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

                $opciones = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                self::$conexion = new PDO($dsn, DB_USER, DB_PASS, $opciones);
            } catch (PDOException $e) {
                if (APP_DEBUG) {
                    die('Error de conexión a la base de datos: ' . $e->getMessage());
                } else {
                    die('No se pudo conectar a la base de datos. Intenta más tarde.');
                }
            }
        }

        return self::$conexion;
    }
}
