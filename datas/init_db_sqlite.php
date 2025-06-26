<?php
declare(strict_types=1);

/**
 * Initialise la base SQLite avec la table 'users'
 */

$dbFile = __DIR__ . '/git.docs.sqlite';
$sqlFile = __DIR__ . '/init_users_sqlite.sql';

if (!file_exists(dirname($dbFile))) {
    mkdir(dirname($dbFile), 0777, true);
}

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);

    echo "✅ Base SQLite initialisée avec succès.
Fichier : {$dbFile}
";
} catch (Throwable $e) {
    echo "❌ Erreur : " . $e->getMessage() . "
";
    exit(1);
}
