<?php
declare(strict_types=1);

namespace App\Security;

final class Security
{
    /**
     * Nettoie et valide une entrée utilisateur selon le type.
     * Exemple:
     * $username = Security::sanitizeInput($_POST['username'] ?? '', 'string');
     * $age      = Security::sanitizeInput($_POST['age'] ?? '', 'int');
     * $email    = Security::sanitizeInput($_POST['email'] ?? '', 'email');
     *
     * @param mixed  $value La donnée à nettoyer.
     * @param string $type  Le type attendu (string, int, float, email, url, bool, raw).
     * @return mixed La donnée nettoyée ou null si invalide.
     */
    public static function sanitizeInput(mixed $value, string $type = 'string'): mixed
    {
        switch ($type) {
            case 'int':
            case 'integer':
                return filter_var($value, FILTER_VALIDATE_INT) !== false ? (int)$value : null;

            case 'float':
                return filter_var($value, FILTER_VALIDATE_FLOAT) !== false ? (float)$value : null;

            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) ?: null;

            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) ?: null;

            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            case 'raw':
                return $value;

            case 'string':
            default:
                return htmlspecialchars(strip_tags(trim((string)$value)), ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * Nettoie un tableau d’entrées utilisateur selon des règles définies.
     * Exemple :
     *$rules = [
     * 'username' => ['type' => 'string', 'min' => 3, 'max' => 20],
     * 'email'    => 'email',
     * 'age'      => ['type' => 'int', 'min' => 18],
     * 'password' => ['type' => 'password', 'min' => 8],
     * 'url'      => ['type' => 'url']
     * ];
     * 
     * $res = Security::sanitizeArray($_POST, $rules);
     * 
     * if ($res['errors']) {
     * foreach ($res['errors'] as $field => $msg) {
     *     echo "<div class='alert alert-danger'>$field : $msg</div>";
     * }
     * } else {
     *     $data = $res['clean'];
     *     // OK pour traitement
     * }   
     * 
     * @param array $input Données brutes (ex: $_POST)
     * @param array $rules Règles associatives ['clé' => 'type']
     * @return array Données nettoyées
     */
    public static function sanitizeArray(array $input, array $rules): array
    {
        $clean = [];
        $errors = [];

        foreach ($rules as $key => $ruleSet) {
            // Support d’un raccourci : 'email' au lieu de ['type' => 'email']
            if (is_string($ruleSet)) {
                $ruleSet = ['type' => $ruleSet];
            }

            $type = $ruleSet['type'] ?? 'string';
            $value = $input[$key] ?? null;

            // Sanitize initial
            $clean[$key] = self::sanitizeInput($value, $type);

            // Validations de base
            switch ($type) {
                case 'int':
                    if (!filter_var($clean[$key], FILTER_VALIDATE_INT)) {
                        $errors[$key] = 'Doit être un nombre entier';
                    }
                    break;

                case 'email':
                    if (!filter_var($clean[$key], FILTER_VALIDATE_EMAIL)) {
                        $errors[$key] = 'Adresse email invalide';
                    }
                    break;

                case 'url':
                    if (!filter_var($clean[$key], FILTER_VALIDATE_URL)) {
                        $errors[$key] = 'URL invalide';
                    }
                    break;

                case 'bool':
                    $clean[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if (!is_bool($clean[$key])) {
                        $errors[$key] = 'Valeur booléenne attendue';
                    }
                    break;

                case 'password':
                    if (strlen((string)$clean[$key]) < ($ruleSet['min'] ?? 6)) {
                        $errors[$key] = 'Mot de passe trop court';
                    }
                    break;

                case 'string':
                default:
                    $str = (string) $clean[$key];
                    $min = $ruleSet['min'] ?? 0;
                    $max = $ruleSet['max'] ?? PHP_INT_MAX;
                    $len = mb_strlen($str);

                    if ($len < $min) {
                        $errors[$key] = "Minimum $min caractères requis";
                    } elseif ($len > $max) {
                        $errors[$key] = "Maximum $max caractères autorisés";
                    }
            }
        }

        return [
            'clean'  => $clean,
            'errors' => $errors
        ];
    }

}
