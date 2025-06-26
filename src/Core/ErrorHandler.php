<?php
declare(strict_types=1);

namespace App\Core;

class ErrorHandler
{
    public static function register(): void
    {
        set_error_handler([self::class, 'handle']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handle(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $message = "[ERROR] $errstr in $errfile on line $errline";
        self::log($message);
        // Optionnel : affichage ou redirection vers une page custom
        return true; // Empêche l'affichage par défaut
    }

    public static function handleException(\Throwable $exception): void
    {
        $message = "[EXCEPTION] " . $exception->getMessage() .
                   " in 1 " . $exception->getFile() .
                   " on line " . $exception->getLine();
        self::log($message);
        http_response_code(500);
        require ROOT_PATH . '/public/errors/error.php';
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR])) {
            $message = "[SHUTDOWN] {$error['message']} in {$error['file']} on line {$error['line']}";
            self::log($message);
            http_response_code(500);
            require ROOT_PATH . '/public/errors/error.php';
        }
    }

    private static function log(string $message, string $level = 'ERROR'): void
    {
        // Choix du fichier en fonction de l’environnement
        $logDir = ROOT_PATH . '/logs/';
        
        // Création du dossier logs/ si nécessaire
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        // Formatage du message
        $entry = sprintf("[%s] %s: %s%s", date('Y-m-d H:i:s'), $level, $message, PHP_EOL);

        // Écriture dans le fichier
        error_log($entry, 3, LOG_PATHFILE);
    }

}
