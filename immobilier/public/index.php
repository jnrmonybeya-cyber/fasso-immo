<?php
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Charger la configuration
require_once __DIR__ . '/../config/config.php';

// Autoloader simple
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    if (strpos($class, $prefix) === 0) {
        $relative_class = substr($class, strlen($prefix));
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

// Récupérer la route
$route = isset($_GET['route']) ? $_GET['route'] : 'home';

// Router simplifié
try {
    switch ($route) {
        case 'login':
            $controller = new App\Controllers\AuthController();
            $controller->login();
            break;
        case 'register':
            $controller = new App\Controllers\AuthController();
            $controller->register();
            break;
        case 'logout':
            $controller = new App\Controllers\AuthController();
            $controller->logout();
            break;
        case 'dashboard':
            $controller = new App\Controllers\DashboardController();
            $controller->index();
            break;
        case 'home':
        default:
            $controller = new App\Controllers\AnnonceController();
            $controller->index();
            break;
        case 'annonce':
            $controller = new App\Controllers\AnnonceController();
            $controller->show();
            break;
        case 'annonce_create':
            $controller = new App\Controllers\AnnonceController();
            $controller->create();
            break;
        case 'annonce_edit':
            $controller = new App\Controllers\AnnonceController();
            $controller->edit();
            break;
        case 'annonce_delete':
            $controller = new App\Controllers\AnnonceController();
            $controller->delete();
            break;
        case 'annonce_validate':
            $controller = new App\Controllers\AnnonceController();
            $controller->validate();
            break;
        case 'annonce_retire':
            $controller = new App\Controllers\AnnonceController();
            $controller->retire();
            break;
        case 'annonce':
            $controller = new App\Controllers\AnnonceController();
            $controller->show();
            break;
        case 'favori_add':
            $controller = new App\Controllers\FavoriController();
            $controller->add();
            break;
        case 'favori_remove':
            $controller = new App\Controllers\FavoriController();
            $controller->remove();
            break;
        case 'visite_demande':
            $controller = new App\Controllers\VisiteController();
            $controller->demande();
            break;
        case 'visite_validate':
            $controller = new App\Controllers\VisiteController();
            $controller->validate();
            break;
        case 'visite_refuse':
            $controller = new App\Controllers\VisiteController();
            $controller->refuse();
            break;
        case 'admin_create_user':
            $controller = new App\Controllers\AdminController();
            $controller->createUser();
            break;
        case 'admin_assign_agent':
            $controller = new App\Controllers\AdminController();
            $controller->assignAgent();
            break;
        case 'photo_delete':
            if (isset($_GET['id']) && isset($_GET['annonce'])) {
                $id = (int)$_GET['id'];
                $annonceId = (int)$_GET['annonce'];
                $photoModel = new App\Models\Photo();
                if ($photoModel->delete($id)) {
                    $_SESSION['flash_message'] = "Photo supprimée.";
                    $_SESSION['flash_type'] = 'success';
                }
                header('Location: index.php?route=annonce_edit&id=' . $annonceId);
                exit;
            }
            break;
    }
} catch (Exception $e) {
    echo '<h1>Erreur :</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}