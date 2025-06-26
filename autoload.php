<?php
declare(strict_types=1);

/**
 * Autoloader maison compatible PSR-4
 * Chargera les classes dans "src/" automatiquement
 */
spl_autoload_register(function (string $class): void {
    // Vérifie que le namespace commence bien par App\
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';

    if (str_starts_with($class, $prefix)) {
        // Supprimer le préfixe
        $relativeClass = substr($class, strlen($prefix));

        // Remplacer les \ par des / et ajouter l'extension
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
        } else {
            // En dev uniquement : log ou message si classe manquante
            if (defined('DEBUG') && DEBUG) {
                error_log("Classe introuvable : $class → fichier attendu : $file");
            }
        }
    }
});

/**
 * ANCIEN CHARGEMENT MANUEL DES FICHIERS
 * À conserver pour référence, mais à ne pas utiliser dans les nouveaux projets.
 */

/*
// Charger les fonctions UI (components)
require_once __DIR__ . '/src/UI/Layout.php';        // La classe statique Layout

// Charger la  la classe Router
require_once __DIR__ . '/src/Router/Router.php';
*/

