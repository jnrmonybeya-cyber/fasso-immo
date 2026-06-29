<?php
namespace App\Helpers;

class Security {
    /**
     * Échapper les données HTML pour prévenir XSS
     */
    public static function escape($data) {
        if (is_array($data)) {
            return array_map([self::class, 'escape'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valider et nettoyer une chaîne
     */
    public static function sanitize($input) {
        return strip_tags(trim($input));
    }
    
    /**
     * Générer un token CSRF
     */
    public static function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Vérifier le token CSRF
     */
    public static function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Hasher un mot de passe avec BCRYPT
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
    }
    
    /**
     * Vérifier un mot de passe
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Régénérer l'ID de session (protection fixation de session)
     */
    public static function regenerateSession() {
        session_regenerate_id(true);
    }
}