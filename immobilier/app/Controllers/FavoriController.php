<?php
namespace App\Controllers;

use App\Models\Favori;
use App\Models\Annonce;

class FavoriController extends BaseController {
    private $favoriModel;
    private $annonceModel;
    
    public function __construct() {
        parent::__construct();
        $this->favoriModel = new Favori();
        $this->annonceModel = new Annonce();
    }
    
    public function add() {
        $this->requireRole('client');
        
        $annonceId = (int)($_GET['id'] ?? 0);
        $clientId = $_SESSION['user_id'];
        
        // Vérifier que l'annonce existe et est publiée
        $annonce = $this->annonceModel->findPublished($annonceId);
        if (!$annonce) {
            $this->setMessage("Annonce non trouvée.", 'error');
            $this->redirect('index.php');
        }
        
        if ($this->favoriModel->add($clientId, $annonceId)) {
            $this->setMessage("Ajouté aux favoris !", 'success');
        } else {
            $this->setMessage("Cette annonce est déjà dans vos favoris.", 'warning');
        }
        
        $this->redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
    }
    
    public function remove() {
        $this->requireRole('client');
        
        $annonceId = (int)($_GET['id'] ?? 0);
        $clientId = $_SESSION['user_id'];
        
        if ($this->favoriModel->remove($clientId, $annonceId)) {
            $this->setMessage("Retiré des favoris.", 'success');
        }
        
        $this->redirect('index.php?route=dashboard');
    }
}