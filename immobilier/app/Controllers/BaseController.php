<?php
namespace App\Controllers;

use App\Helpers\Security;

abstract class BaseController {
    protected $viewData = [];
    protected $currentUser = null;
    
    public function __construct() {
        $this->loadUser();
    }
    
    protected function loadUser() {
        if (isset($_SESSION['user_id'])) {
            $this->currentUser = [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? '',
                'email' => $_SESSION['user_email'] ?? '',
                'type' => $_SESSION['user_type'] ?? ''
            ];
        }
    }
    
    protected function render($view, $data = []) {
        $viewData = array_merge($this->viewData, $data);
        $viewData['user'] = $this->currentUser;
        $viewData['csrf_token'] = Security::generateCsrfToken();
        $viewData['app_url'] = APP_URL;
        
        extract($viewData);
        
        require_once __DIR__ . '/../Views/partials/header.php';
        
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new \Exception("Vue non trouvée : $view");
        }
        
        require_once __DIR__ . '/../Views/partials/footer.php';
    }
    
    protected function redirect($url) {
        // Si l'URL commence par http, la garder
        if (strpos($url, 'http') === 0) {
            header('Location: ' . $url);
        } else {
            // Sinon, construire l'URL complète
            $base = APP_URL . '/public/';
            if (strpos($url, '?') === 0) {
                header('Location: ' . $base . 'index.php' . $url);
            } elseif (strpos($url, 'index.php') === 0 || strpos($url, '?') === 0) {
                header('Location: ' . $base . $url);
            } else {
                header('Location: ' . $base . 'index.php?route=' . $url);
            }
        }
        exit;
    }
    
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
    
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }
    }
    
    protected function requireRole($role) {
        $this->requireAuth();
        if ($_SESSION['user_type'] !== $role) {
            $this->redirect('dashboard');
        }
    }
    
    protected function requireRoles($roles) {
        $this->requireAuth();
        if (!in_array($_SESSION['user_type'], $roles)) {
            $this->redirect('dashboard');
        }
    }
    
    protected function setMessage($message, $type = 'success') {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    
    protected function getMessage() {
        $message = $_SESSION['flash_message'] ?? '';
        $type = $_SESSION['flash_type'] ?? 'success';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    
    protected function validateCsrf($token) {
        if (!Security::verifyCsrfToken($token)) {
            throw new \Exception("Token CSRF invalide.");
        }
    }
}