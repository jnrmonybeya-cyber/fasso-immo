<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Annonce;
use App\Models\Favori;
use App\Models\Visite;
use App\Models\Photo;

class DashboardController extends BaseController {
    private $userModel;
    private $annonceModel;
    private $favoriModel;
    private $visiteModel;
    private $photoModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->annonceModel = new Annonce();
        $this->favoriModel = new Favori();
        $this->visiteModel = new Visite();
        $this->photoModel = new Photo();
    }
    
    public function index() {
        $this->requireAuth();
        
        $userType = $_SESSION['user_type'];
        $userId = $_SESSION['user_id'];
        $data = [];
        
        switch ($userType) {
            case 'client':
                $data = $this->getClientDashboard($userId);
                break;
            case 'bailleur':
                $data = $this->getBailleurDashboard($userId);
                break;
            case 'agent':
                $data = $this->getAgentDashboard();
                break;
            case 'manager':
                $data = $this->getManagerDashboard();
                break;
            default:
                $this->redirect('home');
        }
        
        $data['user_type'] = $userType;
        $data['user_name'] = $_SESSION['user_name'];
        
        // Afficher les messages flash
        if (isset($_SESSION['flash_message'])) {
            $data['flash_message'] = $_SESSION['flash_message'];
            $data['flash_type'] = $_SESSION['flash_type'] ?? 'success';
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
        }
        
        $this->render('dashboard/' . $userType, $data);
    }
    
    private function getClientDashboard($userId) {
        $favorisCount = $this->favoriModel->countByClient($userId);
        $visitesCount = $this->visiteModel->countByClient($userId);
        $favoris = $this->favoriModel->getByClient($userId);
        $visites = $this->visiteModel->getByClient($userId);
        
        // Ajouter les photos pour les favoris
        foreach ($favoris as &$favori) {
            $photo = $this->photoModel->getPrincipal($favori['id']);
            $favori['photo_path'] = $photo ? $photo['photo_path'] : null;
        }
        
        return [
            'favoris_count' => $favorisCount,
            'visites_count' => $visitesCount,
            'favoris' => $favoris,
            'visites' => $visites
        ];
    }
    
    private function getBailleurDashboard($userId) {
        $annonces = $this->annonceModel->getByBailleur($userId);
        $stats = [
            'attente' => 0,
            'publie' => 0,
            'retire' => 0
        ];
        
        // Ajouter les photos
        foreach ($annonces as &$annonce) {
            $photo = $this->photoModel->getPrincipal($annonce['id']);
            $annonce['photo_path'] = $photo ? $photo['photo_path'] : null;
            
            if (isset($stats[$annonce['statut']])) {
                $stats[$annonce['statut']]++;
            }
        }
        
        return [
            'stats' => $stats,
            'annonces' => $annonces
        ];
    }
    
    private function getAgentDashboard() {
        $annoncesAttente = $this->annonceModel->getWaitingForValidation();
        $demandesVisite = $this->visiteModel->getWaiting();
        $annoncesStats = $this->annonceModel->countByStatus();
        
        // Ajouter les photos aux annonces en attente
        foreach ($annoncesAttente as &$annonce) {
            $photo = $this->photoModel->getPrincipal($annonce['id']);
            $annonce['photo_path'] = $photo ? $photo['photo_path'] : null;
        }
        
        return [
            'annonces_attente' => $annoncesAttente,
            'demandes_visite' => $demandesVisite,
            'annonces_publiees' => $annoncesStats['publie'] ?? 0,
            'attente_count' => count($annoncesAttente),
            'demandes_count' => count($demandesVisite)
        ];
    }
    
    private function getManagerDashboard() {
        $usersStats = $this->userModel->getStats();
        $annoncesStats = $this->annonceModel->countByStatus();
        $totalAnnonces = $this->annonceModel->count();
        
        $users = $this->userModel->getUsersByType('client');
        $bailleurs = $this->userModel->getUsersByType('bailleur');
        $agents = $this->userModel->getUsersByType('agent');
        $allUsers = array_merge($users, $bailleurs, $agents);
        
        // Récupérer les annonces en attente
        $annoncesAttente = $this->annonceModel->getByStatus('attente');
        foreach ($annoncesAttente as &$annonce) {
            $photo = $this->photoModel->getPrincipal($annonce['id']);
            $annonce['photo_path'] = $photo ? $photo['photo_path'] : null;
        }
        
        // Récupérer les annonces publiées
        $annoncesPubliees = $this->annonceModel->getByStatus('publie');
        foreach ($annoncesPubliees as &$annonce) {
            $photo = $this->photoModel->getPrincipal($annonce['id']);
            $annonce['photo_path'] = $photo ? $photo['photo_path'] : null;
        }
        
        return [
            'users_stats' => $usersStats,
            'annonces_stats' => $annoncesStats,
            'total_annonces' => $totalAnnonces,
            'users' => $allUsers,
            'annonces_publiees_list' => $annoncesPubliees,
            'annonces_attente_list' => $annoncesAttente,
            'clients_without_agent' => $this->userModel->getClientsWithoutAgent(),
            'agents' => $agents
        ];
    }
}