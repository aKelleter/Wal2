<?php
declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

final class Connection
{
    private static ?PDO $pdo = null;

    /**
     * Intialisation de la connection
     * 
     * @param string $driver 
     * @param array $config 
     * @return void 
     * @throws RuntimeException 
     */
    public static function init(string $driver, array $config): void
    {
        if (self::$pdo) {
            return; // déjà initialisée
        }

        try {
            switch ($driver) {
                case 'sqlite':
                    $dsn = 'sqlite:' . $config['path'];
                    self::$pdo = new PDO($dsn);
                    break;
                case 'mysql':
                    $dsn = sprintf(
                        'mysql:host=%s;dbname=%s;charset=utf8mb4',
                        $config['host'],
                        $config['dbname']
                    );
                    self::$pdo = new PDO($dsn, $config['user'], $config['pass']);
                    break;
                case 'sqlsrv':
                    $dsn = sprintf(
                        'sqlsrv:Server=%s;Database=%s',
                        $config['host'],
                        $config['dbname']
                    );
                    self::$pdo = new PDO($dsn, $config['user'], $config['pass']);
                    break;
                default:
                    throw new RuntimeException("Driver '$driver' non supporté");
            }

            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new RuntimeException("Connexion DB échouée : " . $e->getMessage());
        }
    }

    /**
     * Retourne la connection
     * 
     * @return PDO 
     * @throws RuntimeException 
     */
    public static function get(): PDO
    {
        if (!self::$pdo) {
            throw new RuntimeException('Connection non initialisée, appele Connection::init()');
        }
        return self::$pdo;
    }
}
