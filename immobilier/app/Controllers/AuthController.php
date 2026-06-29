<?php
namespace App\Controllers;

use App\Models\User;
use App\Helpers\Security;
use App\Helpers\Validation;

class AuthController extends BaseController {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function login() {
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }
        
        $errors = [];
        $formData = ['email' => '', 'password' => ''];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData['email'] = Security::sanitize($_POST['email'] ?? '');
            $formData['password'] = $_POST['password'] ?? '';
            
            // Validation
            if (empty($formData['email'])) {
                $errors['email'] = "L'email est requis.";
            } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "L'email n'est pas valide.";
            }
            
            if (empty($formData['password'])) {
                $errors['password'] = "Le mot de passe est requis.";
            }
            
            if (empty($errors)) {
                $user = $this->userModel->findByEmail($formData['email']);
                
                if ($user) {
                    // Vérifier si le mot de passe est en MD5 (ancien format)
                    if (strlen($user['password']) === 32 && ctype_xdigit($user['password'])) {
                        // Migrer le mot de passe MD5 vers BCRYPT
                        if (md5($formData['password']) === $user['password']) {
                            // Migrer vers BCRYPT
                            $this->userModel->update($user['id'], ['password' => $formData['password']]);
                            $user['password'] = Security::hashPassword($formData['password']);
                        }
                    }
                    
                    // Vérification du mot de passe avec BCRYPT
                    if (Security::verifyPassword($formData['password'], $user['password'])) {
                        Security::regenerateSession();
                        
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                        $_SESSION['user_type'] = $user['type_utilisateur'];
                        $_SESSION['user_email'] = $user['email'];
                        
                        $this->setMessage("Bienvenue " . $_SESSION['user_name'] . " !", 'success');
                        $this->redirect('dashboard');
                        exit;
                    } else {
                        $errors['general'] = "Mot de passe incorrect.";
                    }
                } else {
                    $errors['general'] = "Aucun compte trouvé avec cet email.";
                }
            }
        }
        
        $this->render('auth/login', [
            'formData' => $formData,
            'errors' => $errors
        ]);
    }
    
    public function register() {
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }
        
        $errors = [];
        $formData = [
            'nom' => '', 'prenom' => '', 'email' => '',
            'telephone' => '', 'adresse' => '', 'type' => 'client'
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData['nom'] = Security::sanitize($_POST['nom'] ?? '');
            $formData['prenom'] = Security::sanitize($_POST['prenom'] ?? '');
            $formData['email'] = Security::sanitize($_POST['email'] ?? '');
            $formData['telephone'] = Security::sanitize($_POST['telephone'] ?? '');
            $formData['adresse'] = Security::sanitize($_POST['adresse'] ?? '');
            $formData['type'] = Security::sanitize($_POST['type'] ?? 'client');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            
            // Validation
            if (empty($formData['nom'])) {
                $errors['nom'] = "Le nom est requis.";
            } elseif (strlen($formData['nom']) > 100) {
                $errors['nom'] = "Le nom ne doit pas dépasser 100 caractères.";
            }
            
            if (empty($formData['prenom'])) {
                $errors['prenom'] = "Le prénom est requis.";
            } elseif (strlen($formData['prenom']) > 100) {
                $errors['prenom'] = "Le prénom ne doit pas dépasser 100 caractères.";
            }
            
            if (empty($formData['email'])) {
                $errors['email'] = "L'email est requis.";
            } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "L'email n'est pas valide.";
            } else {
                // Vérifier si l'email existe déjà
                $existingUser = $this->userModel->findByEmail($formData['email']);
                if ($existingUser) {
                    $errors['email'] = "Cet email est déjà utilisé.";
                }
            }
            
            if (empty($password)) {
                $errors['password'] = "Le mot de passe est requis.";
            } elseif (strlen($password) < 6) {
                $errors['password'] = "Le mot de passe doit contenir au moins 6 caractères.";
            }
            
            if (empty($password_confirm)) {
                $errors['password_confirm'] = "La confirmation du mot de passe est requise.";
            } elseif ($password !== $password_confirm) {
                $errors['password_confirm'] = "Les mots de passe ne correspondent pas.";
            }
            
            if (!in_array($formData['type'], ['client', 'bailleur'])) {
                $errors['type'] = "Type de compte invalide.";
            }
            
            if (empty($errors)) {
                $data = [
                    'nom' => $formData['nom'],
                    'prenom' => $formData['prenom'],
                    'email' => $formData['email'],
                    'telephone' => $formData['telephone'] ?: null,
                    'adresse' => $formData['adresse'] ?: null,
                    'password' => $password,
                    'type' => $formData['type']
                ];
                
                if ($this->userModel->create($data)) {
                    $this->setMessage("Inscription réussie ! Vous pouvez maintenant vous connecter.", 'success');
                    $this->redirect('login');
                    exit;
                } else {
                    $errors['general'] = "Erreur lors de l'inscription. Veuillez réessayer.";
                }
            }
        }
        
        $this->render('auth/register', [
            'formData' => $formData,
            'errors' => $errors
        ]);
    }
    
    public function logout() {
        $_SESSION = [];
        session_destroy();
        $this->setMessage("Vous avez été déconnecté.", 'success');
        $this->redirect('login');
    }
}