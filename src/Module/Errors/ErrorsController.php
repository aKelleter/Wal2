<?php
declare(strict_types=1);

namespace App\Module\Errors;

use App\UI\Layout;
use App\UI\Template;

final class ErrorsController {

    /**
     * Contrôleur de la page 'index' du module 'errors'
     * 
     * @return void 
     */
    public static function error(): void {

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
    }

    /**
     * Contrôleur de la page 'error500' du module 'errors'
     * 
     * @return void 
     */
    public static function error500(): void {
        
        $pageTitle = 'Error - Git.Docs';

        // -----------------------------------------------
        $tpl = new Template(ROOT_PATH . '/templates/errors');
        $tpl->setFile([
                'main'  => '500.html'    
        ]);

        // Blocs
        $tpl->setVar('Header', Layout::getHeader($pageTitle));
        $tpl->setVar('SectionHeader', Layout::getSectionHeader());
        $tpl->setVar('Navigation', Layout::getNavigation());

        $tpl->setVar('MessageErreur', Layout::message(T_("Erreur 500 - Erreur de serveur interne"), "danger", "text-center"));

        $tpl->setVar('Footer', Layout::getFooter());
        $tpl->setVar('JSSection', Layout::getJSSection());

        // Chaines de traduction spécifiques à la page


        // Inclusion des chaînes de caractères à traduire
        require_once ROOT_PATH . '/locale/strToTranslate.php';

        $tpl->pparse('display', 'main');
    }

    /**
     * Contrôleur de la page 'error404' du module 'errors'
     * 
     * @return void 
     */
    public static function error404(): void {
        $pageTitle = 'Error - Git.Docs';

        // -----------------------------------------------
        $tpl = new Template(ROOT_PATH . '/templates/errors');
        $tpl->setFile([
                'main'  => '404.html'    
        ]);

        // Blocs
        $tpl->setVar('Header', Layout::getHeader($pageTitle));
        $tpl->setVar('SectionHeader', Layout::getSectionHeader());
        $tpl->setVar('Navigation', Layout::getNavigation());

        $tpl->setVar('MessageErreur', Layout::message(T_("Erreur 404 - Cette ressource n'existe pas"), "danger", "text-center"));

        $tpl->setVar('Footer', Layout::getFooter());
        $tpl->setVar('JSSection', Layout::getJSSection());

        // Chaines de traduction spécifiques à la page


        // Inclusion des chaînes de caractères à traduire
        require_once ROOT_PATH . '/locale/strToTranslate.php';

        $tpl->pparse('display', 'main');
    }
}