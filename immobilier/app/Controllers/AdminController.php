<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Annonce;
use App\Helpers\Security;
use App\Helpers\Validation;

class AdminController extends BaseController {
    private $userModel;
    private $annonceModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->annonceModel = new Annonce();
    }
    
    public function createUser() {
        $this->requireRole('manager');
        
        $errors = [];
        $formData = [
            'nom' => '', 'prenom' => '', 'email' => '',
            'telephone' => '', 'adresse' => '', 'type' => 'client'
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier CSRF
            try {
                $this->validateCsrf($_POST['csrf_token'] ?? '');
            } catch (\Exception $e) {
                $errors['general'] = "Token de sécurité invalide.";
                $this->render('admin/create_user', ['formData' => $formData, 'errors' => $errors]);
                return;
            }
            
            $formData['nom'] = Security::sanitize($_POST['nom'] ?? '');
            $formData['prenom'] = Security::sanitize($_POST['prenom'] ?? '');
            $formData['email'] = Security::sanitize($_POST['email'] ?? '');
            $formData['telephone'] = Security::sanitize($_POST['telephone'] ?? '');
            $formData['adresse'] = Security::sanitize($_POST['adresse'] ?? '');
            $formData['type'] = Security::sanitize($_POST['type'] ?? 'client');
            $password = $_POST['password'] ?? '';
            
            $validation = new Validation($formData);
            $validation->required('nom')->maxLength('nom', 100);
            $validation->required('prenom')->maxLength('prenom', 100);
            $validation->required('email')->email('email');
            $validation->required('password')->minLength('password', 6);
            $validation->inArray('type', ['client', 'bailleur', 'agent']);
            
            if ($validation->isValid()) {
                $existingUser = $this->userModel->findByEmail($formData['email']);
                if ($existingUser) {
                    $errors['email'] = "Cet email est déjà utilisé.";
                } else {
                    $data = $formData;
                    $data['password'] = $password;
                    $data['telephone'] = $formData['telephone'] ?? null;
                    $data['adresse'] = $formData['adresse'] ?? null;
                    
                    if ($this->userModel->create($data)) {
                        $this->setMessage("Compte créé avec succès !", 'success');
                        $this->redirect('index.php?route=dashboard');
                    } else {
                        $errors['general'] = "Erreur lors de la création du compte.";
                    }
                }
            } else {
                $errors = $validation->getErrors();
            }
        }
        
        $this->render('admin/create_user', [
            'formData' => $formData,
            'errors' => $errors
        ]);
    }
    
    public function assignAgent() {
        $this->requireRole('manager');
        
        $clientId = (int)($_GET['id'] ?? 0);
        $client = $this->userModel->findById($clientId);
        
        if (!$client || $client['type_utilisateur'] !== 'client') {
            $this->setMessage("Client non trouvé.", 'error');
            $this->redirect('index.php?route=dashboard');
        }
        
        $agents = $this->userModel->getUsersByType('agent');
        $errors = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier CSRF
            try {
                $this->validateCsrf($_POST['csrf_token'] ?? '');
            } catch (\Exception $e) {
                $errors['general'] = "Token de sécurité invalide.";
                $this->render('admin/assign_agent', [
                    'client' => $client,
                    'agents' => $agents,
                    'errors' => $errors
                ]);
                return;
            }
            
            $agentId = (int)($_POST['agent_id'] ?? 0);
            
            if ($agentId) {
                $agent = $this->userModel->findById($agentId);
                if ($agent && $agent['type_utilisateur'] === 'agent') {
                    if ($this->userModel->update($clientId, ['agent_id' => $agentId])) {
                        $this->setMessage("Agent assigné avec succès !", 'success');
                        $this->redirect('index.php?route=dashboard');
                    } else {
                        $errors['general'] = "Erreur lors de l'assignation.";
                    }
                } else {
                    $errors['general'] = "Agent non trouvé.";
                }
            } else {
                $errors['general'] = "Veuillez sélectionner un agent.";
            }
        }
        
        $this->render('admin/assign_agent', [
            'client' => $client,
            'agents' => $agents,
            'errors' => $errors
        ]);
    }
}