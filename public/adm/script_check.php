<?php
declare(strict_types=1);
define('ROOT', dirname(__DIR__, 2));
/**
 * Fichier de diagnostic rapide pour Git.Docs
 * Affiche des infos utiles pour détecter les problèmes d'environnement
 */

header('Content-Type: text/plain');

echo "=== GIT.DOCS ENV CHECK ===\n\n";

// PHP version
echo "PHP Version: " . PHP_VERSION . "\n";

// Apache mod_rewrite (test indirect)
echo "ROOT DIRECTORY : ". ROOT ."\n";

$htaccessActive = file_exists(ROOT . '/.htaccess') ? 'Yes' : 'No';
echo "HTACCESS file in current dir: $htaccessActive\n";

// Vérification du fichier de configuration
$confPath = ROOT . '/config/conf.php';
if (file_exists($confPath)) {
    echo "conf.php: Found at config/conf.php\n";
} else {
    echo "conf.php: ❌ NOT FOUND at config/conf.php\n";
}

// BASE_URL test
require_once ROOT . '/bootstrap.php';

echo "BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'Not defined') . "\n";

// Autoload test
try {
    class_exists('App\\UI\\Layout')
        ? print("Autoload OK: Layout class found\n")
        : print("Autoload FAIL: Layout class not found\n");
} catch (Throwable $e) {
    echo "Autoload ERROR: " . $e->getMessage() . "\n";
}

// Apache env (Windows only)
if (isset($_SERVER['SERVER_SOFTWARE'])) {
    echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
}

// ==== Gettext PHP Extension ====

echo "\n=== GETTEXT CHECK ===\n";

// 4. Vérification dossiers et fichiers .mo attendus
$localesToCheck = ['fr_BE', 'en_US', 'nl_BE']; // Ajoute ici tes codes langues
$localeDir = ROOT . '/locale';

foreach ($localesToCheck as $loc) {
    $moFile = "$localeDir/$loc/LC_MESSAGES/messages.mo";
    if (file_exists($moFile)) {
        echo "MO file for $loc: OK ($moFile)\n";
    } else {
        echo "MO file for $loc: ❌ MISSING ($moFile)\n";
    }
}

echo "\nGettext check completed.\n";

echo "\nCheck completed.\n";
