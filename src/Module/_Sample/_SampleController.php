<?php
declare(strict_types=1);

namespace App\Module\_Sample;

use App\Security\AccessControl;
use App\Security\Csrf;
use App\UI\View;
use App\UI\Template;
use App\UI\Layout;
use App\UI\Url;

final class SampleController
{
    public static function nomDeLaPage(): void
    {
        // Sécurité (authentification / autorisation si nécessaire)
        AccessControl::requireLogin();

        // Titre de la page
        $pageTitle = 'Titre de la page';

        // Message flash éventuel (affiché une fois après une redirection)
        $message = View::consumeFlashMessage();

        // Traitement d’action GET/POST (ex : suppression, validation, etc.)
        if (isset($_GET['action'], $_GET['token'])) {
            if (Csrf::verifyToken($_GET['token'])) {
                // ... traitement ...
                $message = '<div class="alert alert-success">Action réussie</div>';
            } else {
                $message = '<div class="alert alert-danger">Jeton CSRF invalide.</div>';
            }

            View::redirectWithMessage(BASE_URL . '/chemin/page', $message);
        }

        // Token CSRF pour la page
        $csrfToken = Csrf::generateToken();

        // ---------------------------------------------------------------------------------    
        
        // Vue
        $tpl = new Template(ROOT_PATH . '/templates/nom-du-module');
        $tpl->setFile(['main' => 'fichier.html']);

        // Variables passées à la vue
        $tpl->setVar([
            'Header'        => Layout::getHeader($pageTitle),
            'SectionHeader' => Layout::getSectionHeader(),
            'Navigation'    => Layout::getNavigation(),

            // Contenu dynamique spécifique à la page
            'MonContenu'    => '... contenu ou appel de rendu (View::renderXyz()) ...',
            'CsrfToken'     => $csrfToken,
            'Message'       => $message,

            // Liens utiles
            'UrlRetour'     => Url::format('/chemin/page', [], true),

            'Footer'        => Layout::getFooter(),
            'JSSection'     => Layout::getJSSection(),
        ]);

        // Affichage
        $tpl->pparse('display', 'main');
    }
}