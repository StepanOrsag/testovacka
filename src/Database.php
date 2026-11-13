<?php

class Database
{
    private Static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if(self::$connection == null) {
            $host = getenv('DB_HOST') ?: 'db';
            $port = getenv('DB_PORT') ?: '5432';
            $dbname = getenv('DB_NAME') ?: 'parcel_db';
            $user = getenv('DB_USER') ?: 'viagem_user';
            $password = getenv('DB_PASS') ?: 'secretpassword';

            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

            try {
                self::$connection = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                throw new RuntimeException("Chyba připojení k databázi" . $e->getMessage(), 0, $e);
            }
        }

        return self::$connection;
    }
}