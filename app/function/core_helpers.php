<?php
declare(strict_types=1);  

use App\I18n\I18n;

/**
 * collection de fonctions utilitaires
 */

/**
 * Alias de I18n::t()
 * 
 * @param string $msg 
 * @return string 
 */ 
function T_(string $msg) : string
{
     return I18n::t($msg); 
}

/**
 * Affiche les variables avec var_dump() dans une balise <pre> pour un affichage lisible
 * 
 * @param mixed ...$args 
 * @return void 
 */
function vdump(...$args) : void
{
    echo '<pre class="vdump">';
    foreach ($args as $arg) {
        var_dump($arg);
    }
    echo '</pre>';
}

/**
 * Affiche les variables avec print_r() dans une balise <pre> pour un affichage lisible
 * 
 * @param mixed ...$args 
 * @return void 
 */
function prtr(...$args) : void
{
    echo '<pre class="prtr">';
    foreach ($args as $arg) {
        print_r($arg);
    }
    echo '</pre>';
}