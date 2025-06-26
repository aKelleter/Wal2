<?php
declare(strict_types=1);

// Définition des constantes
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', __DIR__);

// Autoloader d'abord (important si la classe Connection est utilisée ensuite)
require_once ROOT_PATH . '/autoload.php';

// Chargement des configurations
require_once ROOT_PATH . '/config/conf.php';
require_once ROOT_PATH . '/config/database.php';
// Chargement des fichiers de fonctions
require_once ROOT_PATH . '/app/function/app-fct.php'; 

// Chargement des classes nécessaires
use App\Database\Connection;
use App\Auth\Auth;
use App\I18n\I18n;
use App\Helpers\Helpers;
use App\Core\ErrorHandler;

// Chargement des classe Gettext sans Composer
Helpers::registerGettext();

// Initialisation de la connexion à la base de données
Connection::init(
    DB_DRIVER,
    [
        'path' => ROOT_PATH . '/datas/' . DB_NAME,
        'host' => DB_HOST,
        'dbname' => DB_NAME,
        'user' => DB_USER,
        'pass' => DB_PASS,
    ]
);

 
// Initialisation du système d'authentification  avec Auth et l'objet PDO partagé
Auth::init(Connection::get());

// Intialisation de la langue
I18n::init(); // ou I18n::init('en_US') pour forcer la langue
//DEBUG// echo I18n::getLang();

// Configure tout le système de gestion d’erreurs de l'application
// Active ou non l'affichage des erreurs
if (DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

ErrorHandler::register();





