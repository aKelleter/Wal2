<?php
declare(strict_types=1);  

use App\Module\Errors\ErrorsController;

// Méthode OO MVC
ErrorsController::error();


// ANCIENNE METHODE (mais valide également)
/*
use App\UI\Layout;
use App\UI\Template;

$pageTitle = 'Error - Git.Docs';

// -----------------------------------------------
$tpl = new Template(ROOT_PATH . '/templates/errors');
$tpl->setFile([
        'main'  => 'error.html'    
]);

// Blocs
$tpl->setVar('Header', Layout::getHeader($pageTitle));
$tpl->setVar('SectionHeader', Layout::getSectionHeader());
$tpl->setVar('Navigation', Layout::getNavigation());

$tpl->setVar('MessageErreur', Layout::message(T_("Erreur - L'application a généré une erreur, celle-ci a été consignée"), "danger", "text-center"));

$tpl->setVar('Footer', Layout::getFooter());
$tpl->setVar('JSSection', Layout::getJSSection());

// Chaines de traduction spécifiques à la page


// Inclusion des chaînes de caractères à traduire
require_once ROOT_PATH . '/locale/strToTranslate.php';

$tpl->pparse('display', 'main');
*/