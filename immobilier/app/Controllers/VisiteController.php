<?php
namespace App\Controllers;

use App\Models\Annonce;
use App\Models\Visite;
use App\Helpers\Security;
use App\Helpers\Validation;

class VisiteController extends BaseController {
    private $annonceModel;
    private $visiteModel;
    
    public function __construct() {
        parent::__construct();
        $this->annonceModel = new Annonce();
        $this->visiteModel = new Visite();
    }
    
    public function demande() {
        $this->requireRole('client');
        
        $annonceId = (int)($_GET['id'] ?? 0);
        $annonce = $this->annonceModel->findPublished($annonceId);
        
        if (!$annonce) {
            $this->setMessage("Annonce non trouvée.", 'error');
            $this->redirect('index.php');
        }
        
        $errors = [];
        $formData = [
            'date' => '',
            'heure' => '',
            'message' => ''
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier CSRF
            try {
                $this->validateCsrf($_POST['csrf_token'] ?? '');
            } catch (\Exception $e) {
                $errors['general'] = "Token de sécurité invalide.";
                $this->render('visites/demande', ['formData' => $formData, 'errors' => $errors, 'annonce' => $annonce]);
                return;
            }
            
            $formData['date'] = Security::sanitize($_POST['date'] ?? '');
            $formData['heure'] = Security::sanitize($_POST['heure'] ?? '');
            $formData['message'] = Security::sanitize($_POST['message'] ?? '');
            
            $validation = new Validation($formData);
            $validation->required('date')->date('date')->dateAfter('date', date('Y-m-d'));
            $validation->required('heure');
            $validation->maxLength('message', 500);
            
            if ($validation->isValid()) {
                $data = [
                    'client_id' => $_SESSION['user_id'],
                    'annonce_id' => $annonceId,
                    'date_visite' => $formData['date'],
                    'heure_visite' => $formData['heure'],
                    'message' => $formData['message']
                ];
                
                if ($this->visiteModel->create($data)) {
                    $this->setMessage("Demande de visite envoyée avec succès !", 'success');
                    $this->redirect('index.php?route=dashboard');
                } else {
                    $errors['general'] = "Erreur lors de l'envoi de la demande.";
                }
            } else {
                $errors = $validation->getErrors();
            }
        }
        
        $this->render('visites/demande', [
            'formData' => $formData,
            'errors' => $errors,
            'annonce' => $annonce
        ]);
    }
    
    public function validate() {
        $this->requireRole('agent');
        
        $id = (int)($_GET['id'] ?? 0);
        
        if ($this->visiteModel->validate($id)) {
            $this->setMessage("Demande de visite validée.", 'success');
        } else {
            $this->setMessage("Erreur lors de la validation.", 'error');
        }
        
        $this->redirect('index.php?route=dashboard');
    }
    
    public function refuse() {
        $this->requireRole('agent');
        
        $id = (int)($_GET['id'] ?? 0);
        
        if ($this->visiteModel->refuse($id)) {
            $this->setMessage("Demande de visite refusée.", 'success');
        } else {
            $this->setMessage("Erreur lors du refus.", 'error');
        }
        
        $this->redirect('index.php?route=dashboard');
    }
}