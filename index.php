<?php
declare(strict_types=1);

/**
 * Point d'entrée simplifié 
 * -------------------------
 * Ce fichier redirige silencieusement vers le vrai point d'entrée
 * situé dans le dossier "public/", sans modifier l'URL affichée.
 * 
 * Avantages :
 * - Aucun besoin de configurer Apache ou faire un lien symbolique
 * - Compatible avec un hébergement classique
 * - Permet de garder une structure professionnelle (public/, src/, etc.)
 */

// Se déplacer dans le dossier "public"
chdir(__DIR__ . '/public');

// Charger le vrai point d’entrée
require_once 'index.php';



