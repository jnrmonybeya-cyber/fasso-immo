<?php
namespace App\Controllers;

use App\Models\Annonce;
use App\Models\Photo;
use App\Models\Favori;
use App\Helpers\Security;
use App\Helpers\Validation;
use App\Helpers\Upload;

class AnnonceController extends BaseController {
    private $annonceModel;
    private $photoModel;
    private $favoriModel;
    
    public function __construct() {
        parent::__construct();
        $this->annonceModel = new Annonce();
        $this->photoModel = new Photo();
        $this->favoriModel = new Favori();
    }
    
    public function index() {
        $filters = [
            'zone' => Security::sanitize($_GET['zone'] ?? ''),
            'type_bien' => Security::sanitize($_GET['type_bien'] ?? '')
        ];
        
        $annonces = $this->annonceModel->getAllPublished($filters);
        
        foreach ($annonces as &$annonce) {
            $photo = $this->photoModel->getPrincipal($annonce['id']);
            $annonce['photo_path'] = $photo ? $photo['photo_path'] : null;
            
            // Vérifier si dans les favoris pour l'utilisateur connecté
            if ($this->isAuthenticated() && $_SESSION['user_type'] === 'client') {
                $annonce['is_favori'] = $this->favoriModel->isFavori($_SESSION['user_id'], $annonce['id']);
            }
        }
        
        $this->render('home', [
            'annonces' => $annonces,
            'filters' => $filters
        ]);
    }
    
    public function show() {
        $id = (int)($_GET['id'] ?? 0);
        
        $annonce = $this->annonceModel->findPublished($id);
        if (!$annonce) {
            $this->setMessage("Annonce non trouvée.", 'error');
            $this->redirect('index.php');
        }
        
        // Incrémenter le nombre de vues
        $this->annonceModel->incrementViews($id);
        
        $photos = $this->photoModel->getByAnnonce($id);
        
        $this->render('annonces/show', [
            'annonce' => $annonce,
            'photos' => $photos
        ]);
    }
    
    public function create() {
        $this->requireRoles(['bailleur', 'agent']);
        
        $errors = [];
        $formData = [
            'titre' => '', 'description' => '', 'type_bien' => '',
            'zone_geographique' => '', 'prix' => '', 'surface' => ''
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier CSRF
            try {
                $this->validateCsrf($_POST['csrf_token'] ?? '');
            } catch (\Exception $e) {
                $errors['general'] = "Token de sécurité invalide.";
                $this->render('annonces/create', ['formData' => $formData, 'errors' => $errors]);
                return;
            }
            
            $formData['titre'] = Security::sanitize($_POST['titre'] ?? '');
            $formData['description'] = Security::sanitize($_POST['description'] ?? '');
            $formData['type_bien'] = Security::sanitize($_POST['type_bien'] ?? '');
            $formData['zone_geographique'] = Security::sanitize($_POST['zone'] ?? '');
            $formData['prix'] = $_POST['prix'] ?? '';
            $formData['surface'] = $_POST['surface'] ?? '';
            
            $validation = new Validation($formData);
            $validation->required('titre')->maxLength('titre', 255);
            $validation->required('description');
            $validation->required('type_bien')->inArray('type_bien', ['location', 'vente', 'bureau', 'espace_vide']);
            $validation->required('zone_geographique')->maxLength('zone_geographique', 200);
            $validation->required('prix')->numeric('prix');
            
            if (!empty($formData['surface'])) {
                $validation->integer('surface');
            }
            
            if ($validation->isValid()) {
                $statut = ($_SESSION['user_type'] === 'agent') ? 'publie' : 'attente';
                
                $data = [
                    'bailleur_id' => $_SESSION['user_id'],
                    'titre' => $formData['titre'],
                    'description' => $formData['description'],
                    'type_bien' => $formData['type_bien'],
                    'zone' => $formData['zone_geographique'],
                    'prix' => (float)$formData['prix'],
                    'surface' => !empty($formData['surface']) ? (int)$formData['surface'] : null,
                    'statut' => $statut
                ];
                
                $annonceId = $this->annonceModel->create($data);
                
                if ($annonceId) {
                    // Gestion des photos
                    if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
                        $upload = new Upload();
                        $uploadedFiles = $upload->uploadMultiple($_FILES['photos'], '');
                        
                        $photoErrors = $upload->getErrors();
                        if (!empty($photoErrors)) {
                            $this->setMessage("Annonce créée mais erreurs lors de l'upload des photos: " . implode(', ', $photoErrors), 'warning');
                        }
                        
                        foreach ($uploadedFiles as $index => $filename) {
                            $isPrincipal = ($index === 0);
                            $this->photoModel->create([
                                'annonce_id' => $annonceId,
                                'photo_path' => $filename,
                                'is_principal' => $isPrincipal ? 1 : 0,
                                'ordre' => $index
                            ]);
                        }
                    }
                    
                    $message = $statut === 'attente' ? 
                        "Annonce créée et en attente de validation par un agent." : 
                        "Annonce publiée avec succès.";
                    $this->setMessage($message, 'success');
                    $this->redirect('index.php?route=dashboard');
                } else {
                    $errors['general'] = "Erreur lors de la création de l'annonce.";
                }
            } else {
                $errors = $validation->getErrors();
            }
        }
        
        $this->render('annonces/create', [
            'formData' => $formData,
            'errors' => $errors
        ]);
    }
    
    public function edit() {
        $this->requireRoles(['bailleur', 'agent']);
        
        $id = (int)($_GET['id'] ?? 0);
        $annonce = $this->annonceModel->findById($id);
        
        if (!$annonce || ($annonce['bailleur_id'] != $_SESSION['user_id'] && $_SESSION['user_type'] !== 'agent')) {
            $this->setMessage("Accès non autorisé ou annonce non trouvée.", 'error');
            $this->redirect('index.php?route=dashboard');
        }
        
        $errors = [];
        $formData = [
            'titre' => $annonce['titre'],
            'description' => $annonce['description'],
            'type_bien' => $annonce['type_bien'],
            'zone' => $annonce['zone_geographique'],
            'prix' => $annonce['prix'],
            'surface' => $annonce['surface']
        ];
        $photos = $this->photoModel->getByAnnonce($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier CSRF
            try {
                $this->validateCsrf($_POST['csrf_token'] ?? '');
            } catch (\Exception $e) {
                $errors['general'] = "Token de sécurité invalide.";
                $this->render('annonces/edit', ['formData' => $formData, 'errors' => $errors, 'annonce' => $annonce, 'photos' => $photos]);
                return;
            }
            
            $formData['titre'] = Security::sanitize($_POST['titre'] ?? '');
            $formData['description'] = Security::sanitize($_POST['description'] ?? '');
            $formData['type_bien'] = Security::sanitize($_POST['type_bien'] ?? '');
            $formData['zone'] = Security::sanitize($_POST['zone'] ?? '');
            $formData['prix'] = $_POST['prix'] ?? '';
            $formData['surface'] = $_POST['surface'] ?? '';
            
            $validation = new Validation($formData);
            $validation->required('titre')->maxLength('titre', 255);
            $validation->required('description');
            $validation->required('type_bien')->inArray('type_bien', ['location', 'vente', 'bureau', 'espace_vide']);
            $validation->required('zone')->maxLength('zone', 200);
            $validation->required('prix')->numeric('prix');
            
            if (!empty($formData['surface'])) {
                $validation->integer('surface');
            }
            
            if ($validation->isValid()) {
                // Si c'est un bailleur et l'annonce était publiée, on la remet en attente
                $statut = $annonce['statut'];
                if ($_SESSION['user_type'] === 'bailleur' && $annonce['statut'] === 'publie') {
                    $statut = 'attente';
                }
                
                $updateData = [
                    'titre' => $formData['titre'],
                    'description' => $formData['description'],
                    'type_bien' => $formData['type_bien'],
                    'zone_geographique' => $formData['zone'],
                    'prix' => (float)$formData['prix'],
                    'surface' => !empty($formData['surface']) ? (int)$formData['surface'] : null,
                    'statut' => $statut
                ];
                
                if ($this->annonceModel->update($id, $updateData)) {
                    // Gestion des photos
                    if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
                        $upload = new Upload();
                        $uploadedFiles = $upload->uploadMultiple($_FILES['photos'], '');
                        
                        $photoErrors = $upload->getErrors();
                        if (!empty($photoErrors)) {
                            $this->setMessage("Annonce modifiée mais erreurs lors de l'upload des photos: " . implode(', ', $photoErrors), 'warning');
                        }
                        
                        $currentCount = $this->photoModel->countByAnnonce($id);
                        foreach ($uploadedFiles as $index => $filename) {
                            $isPrincipal = ($currentCount === 0 && $index === 0);
                            $this->photoModel->create([
                                'annonce_id' => $id,
                                'photo_path' => $filename,
                                'is_principal' => $isPrincipal ? 1 : 0,
                                'ordre' => $currentCount + $index
                            ]);
                        }
                    }
                    
                    $message = $statut === 'attente' ? 
                        "Annonce modifiée et remise en attente de validation." : 
                        "Annonce modifiée avec succès.";
                    $this->setMessage($message, 'success');
                    $this->redirect('index.php?route=dashboard');
                } else {
                    $errors['general'] = "Erreur lors de la modification de l'annonce.";
                }
            } else {
                $errors = $validation->getErrors();
            }
        }
        
        $this->render('annonces/edit', [
            'formData' => $formData,
            'errors' => $errors,
            'annonce' => $annonce,
            'photos' => $photos
        ]);
    }
    
    public function delete() {
        $this->requireRoles(['bailleur', 'agent']);
        
        $id = (int)($_GET['id'] ?? 0);
        $annonce = $this->annonceModel->findById($id);
        
        if (!$annonce || ($annonce['bailleur_id'] != $_SESSION['user_id'] && $_SESSION['user_type'] !== 'agent')) {
            $this->setMessage("Accès non autorisé ou annonce non trouvée.", 'error');
            $this->redirect('index.php?route=dashboard');
        }
        
        if ($this->annonceModel->delete($id)) {
            $this->setMessage("Annonce supprimée avec succès.", 'success');
        } else {
            $this->setMessage("Erreur lors de la suppression de l'annonce.", 'error');
        }
        
        $this->redirect('index.php?route=dashboard');
    }

    public function validate() {
        $this->requireRole('agent');
        
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id == 0) {
            $this->setMessage("ID d'annonce invalide.", 'error');
            $this->redirect('dashboard');
            return;
        }
        
        // Vérifier que l'annonce existe et est en attente
        $annonce = $this->annonceModel->findById($id);
        
        if (!$annonce) {
            $this->setMessage("Annonce non trouvée.", 'error');
            $this->redirect('dashboard');
            return;
        }
        
        if ($annonce['statut'] !== 'attente') {
            $this->setMessage("Cette annonce n'est pas en attente de validation.", 'warning');
            $this->redirect('dashboard');
            return;
        }
        
        // Valider l'annonce
        if ($this->annonceModel->validate($id)) {
            $this->setMessage("✅ Annonce '{$annonce['titre']}' validée et publiée avec succès !", 'success');
        } else {
            $this->setMessage("❌ Erreur lors de la validation de l'annonce.", 'error');
        }
        
        $this->redirect('dashboard');
    }
    
    public function retire() {
        $this->requireRole('manager');
        
        $id = (int)($_GET['id'] ?? 0);
        
        if ($this->annonceModel->retire($id)) {
            $this->setMessage("Annonce retirée avec succès.", 'success');
        } else {
            $this->setMessage("Erreur lors du retrait de l'annonce.", 'error');
        }
        
        $this->redirect('index.php?route=dashboard');
    }
}