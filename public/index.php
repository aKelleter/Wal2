<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use App\Router\Router;
use App\UI\Assets;

// Ajoute les styles GLOBAUX utilses à toutes les pages
Assets::addStyle(BASE_URL."/vendor/bootstrap/css/bootstrap.min.css");
Assets::addStyle(BASE_URL."/vendor/highlight/css/github-dark.min.css");

// Preload et ajout des CSS de l'App
Assets::addPreload(BASE_URL."/assets/css/styles.css", 'style');
Assets::addStyle(BASE_URL."/assets/css/styles.css");

// Ajoute les scripts GLOBAUX utiles à toutes les pages
Assets::addScript(BASE_URL."/vendor/bootstrap/js/bootstrap.bundle.min.js");
Assets::addScript(BASE_URL."/vendor/highlight/js/highlight.min.js");
Assets::addScript(BASE_URL."/assets/js/main.js", true, true);


// Rendu de la page
$content = Router::render();
//DEBUG// $page = Router::resolve(); echo 'PAGE : ' . $page; 
//DEBUG// echo "Langue détectée : " . \App\I18n\I18n::getLang();
echo $content;    
