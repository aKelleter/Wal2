<?php
declare(strict_types=1);

namespace App\View;

use App\Model\SampleArticleModel;
use App\Security\Csrf;
use App\UI\Url;

final class SampleArticleView
{
    /**
     * Génère la liste HTML des articles (table responsive Bootstrap)
     *
     * @return string
     */
    public static function renderList(): string
    {
        $articles = SampleArticleModel::getAll();
        if (empty($articles)) {
            return '<div class="alert alert-info text-center">Aucun article pour le moment.</div>';
        }

        $html = '<div class="table-responsive"><table class="table table-striped table-hover">';
        $html .= '<thead><tr><th>#</th><th>Titre</th><th>Date</th><th>Actions</th></tr></thead><tbody>';

        foreach ($articles as $article) {
            $editUrl = Url::format('/article/edit', ['id' => $article['id']], true);
            $delUrl  = Url::format('/article/delete', ['id' => $article['id'], 'token' => Csrf::generateToken()], true);

            $html .= '<tr>';
            $html .= '<td>' . $article['id'] . '</td>';
            $html .= '<td>' . htmlspecialchars($article['title']) . '</td>';
            $html .= '<td>' . date('d/m/Y H:i', strtotime($article['created_at'])) . '</td>';
            $html .= '<td>';
            $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-outline-primary me-2">Éditer</a>';
            $html .= '<a href="' . $delUrl . '" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Supprimer cet article ?\')">Supprimer</a>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    /**
     * Génère le formulaire HTML pour créer ou éditer un article
     *
     * @param array|null $article
     * @return string
     */
    public static function renderForm(?array $article = null): string
    {
        $id = $article['id'] ?? '';
        $title = $article['title'] ?? '';
        $content = $article['content'] ?? '';
        $token = Csrf::generateToken();

        $action = $id ? Url::format('/article/update', ['id' => $id], true) : Url::format('/article/create', [], true);

        return <<<HTML
<form method="post" action="{$action}">
    <input type="hidden" name="csrf_token" value="{$token}">
    <div class="mb-3">
        <label for="title" class="form-label">Titre</label>
        <input type="text" class="form-control" id="title" name="title" required value="{$title}">
    </div>
    <div class="mb-3">
        <label for="content" class="form-label">Contenu</label>
        <textarea class="form-control" id="content" name="content" rows="6" required>{$content}</textarea>
    </div>
    <div class="text-end">
        <button type="submit" class="btn btn-success">Enregistrer</button>
    </div>
</form>
HTML;
    }

    /**
     * Génère l'affichage du détail de l'article
     * 
     * @param mixed $article 
     * @return string 
     */
    public static function renderDetail($article): string
    {
        $string = '';
        // Votre code ici : présentation détaillée de l'article reçu en paramètre

        return $string;
    }
}
