<?php
namespace App\Models;

use App\Config\Database;
use App\Helpers\Security;

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        // Hachage du mot de passe avec BCRYPT
        $data['password'] = Security::hashPassword($data['password']);
        
        $sql = "INSERT INTO users (nom, prenom, email, telephone, adresse, password, type_utilisateur) 
                VALUES (:nom, :prenom, :email, :telephone, :adresse, :password, :type)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'] ?? null,
            'adresse' => $data['adresse'] ?? null,
            'password' => $data['password'],
            'type' => $data['type']
        ]);
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];
        
        $allowedFields = ['nom', 'prenom', 'email', 'telephone', 'adresse', 'agent_id'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = "password = :password";
            $params['password'] = Security::hashPassword($data['password']);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function getUsersByType($type) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE type_utilisateur = :type ORDER BY created_at DESC");
        $stmt->execute(['type' => $type]);
        return $stmt->fetchAll();
    }
    
    public function getStats() {
        $stmt = $this->db->query("SELECT type_utilisateur, COUNT(*) as count FROM users GROUP BY type_utilisateur");
        $stats = [];
        while ($row = $stmt->fetch()) {
            $stats[$row['type_utilisateur']] = $row['count'];
        }
        return $stats;
    }
    
    public function getClientsWithoutAgent() {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE type_utilisateur = 'client' AND agent_id IS NULL");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Fonction pour migrer les anciens mots de passe MD5 vers BCRYPT
    public function migratePassword($id, $md5Password) {
        $bcryptPassword = Security::hashPassword($md5Password);
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute(['password' => $bcryptPassword, 'id' => $id]);
    }
}