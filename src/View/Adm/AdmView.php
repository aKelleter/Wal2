<?php
declare(strict_types=1);

namespace App\View\Adm;

use App\Core\CacheManager;
use App\Security\Csrf;

final class AdmView
{
    public static function renderListCachedFiles(string $csrfToken): string
    {
        $files = CacheManager::listCacheFiles();        
 
        if (empty($files)) {
            return '
            <div class="div-message bg-light border border-secondary rounded p-3 mb-3">
                <h5>Liste des fichiers mis en cache</h5>
                <hr>
                <p class="text-muted">Aucun fichier de cache n\'est présent pour le moment.</p>
            </div>';
        }
 
        $totalSize = 0;
        $count = count($files);
 
        $html = '
        <div class="div-message bg-info border border-info rounded p-3 mb-3">
            <h5>Liste des fichiers mis en cache</h5>
            <hr>
            <ul class="list-group mb-3">';
 
        foreach ($files as $file) {
            $basename = basename($file);
            $url = '?delete=' . urlencode($basename) . '&token=' . urlencode($csrfToken);
            $sizeBytes = filesize($file);
            $totalSize += $sizeBytes;
 
            $size = number_format($sizeBytes / 1024, 2) . ' Ko';
            $date = date('d/m/Y H:i:s', filemtime($file));
 
            $html .= '<li class="list-group-item d-flex justify-content-between align-items-center small">';
            $html .= '<div><strong>' . $basename . '</strong><br><small>' . $size . ' – modifié le ' . $date . '</small></div>';
            $html .= '<a href="' . $url . '" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Supprimer ce cache ?\')">Supprimer</a>';
            $html .= '</li>';
        }
 
        $totalSizeFormatted = number_format($totalSize / 1024, 2) . ' Ko';
 
        $html .= '</ul>';
        $html .= '<p class="small text-muted">Total : ' . $count . ' fichier(s) – ' . $totalSizeFormatted . '</p>';
        $html .= '</div>';
 
        return $html;
    }
}