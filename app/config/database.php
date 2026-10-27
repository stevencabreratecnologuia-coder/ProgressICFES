<?php

class Database
{
    private static string $host =
    "mysql-2834a8ac-steven-27ff.k.aivencloud.com";

    private static int $port = 19514;

    private static string $db_name =
    "progressicfes_restauracion2";

    private static string $username = "avnadmin";

    private static string $password = "";

    public static function connect(): PDO
    {
        try {
            // En Render, el certificado se proporciona como archivo secreto.
            // En local, se utiliza ca.pem junto a este archivo.
            $caPath = getenv('RENDER')
                ? '/etc/secrets/ca.pem'
                : __DIR__ . DIRECTORY_SEPARATOR . 'ca.pem';

            if (!is_readable($caPath)) {
                throw new RuntimeException(
                    'No se encuentra el certificado CA.'
                );
            }

            $host = getenv('DB_HOST') ?: self::$host;
            $port = (int) (getenv('DB_PORT') ?: self::$port);
            $database = getenv('DB_NAME') ?: self::$db_name;
            $username = getenv('DB_USER') ?: self::$username;
            $password = getenv('DB_PASSWORD') ?: self::$password;

            if ($password === '') {
                throw new RuntimeException(
                    'Falta configurar DB_PASSWORD.'
                );
            }

            $dsn = "mysql:host={$host};port={$port}"
                . ";dbname={$database};charset=utf8mb4"
                . ";sslmode=verify-ca"
                . ";sslrootcert='{$caPath}'";

            return new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (Throwable $e) {
            error_log((string) $e);
            die('No se pudo conectar con la base de datos.');
        }
    }
}
