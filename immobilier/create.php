<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';
require_once 'app/Helpers/Security.php';

use App\Helpers\Security;

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Supprimer les anciens comptes de test
    $pdo->exec("DELETE FROM users WHERE email IN ('manager1@immobilier.com', 'agent@immobilier.com', 'nemata@gmail.com', 'innocent@gmail.com')");
    
    // Créer les comptes avec BCRYPT
    $accounts = [
        [
            'nom' => 'Manager',
            'prenom' => 'Test',
            'email' => 'manager1@immobilier.com',
            'telephone' => '70123456',
            'adresse' => 'Ouagadougou',
            'password' => Security::hashPassword('123456'),
            'type' => 'manager'
        ],
        [
            'nom' => 'Agent',
            'prenom' => 'Test',
            'email' => 'agent@immobilier.com',
            'telephone' => '70123457',
            'adresse' => 'Ouagadougou',
            'password' => Security::hashPassword('123456'),
            'type' => 'agent'
        ],
        [
            'nom' => 'OUEDRAOGO',
            'prenom' => 'nemata',
            'email' => 'nemata@gmail.com',
            'telephone' => '76920189',
            'adresse' => 'KILWIN',
            'password' => Security::hashPassword('123456'),
            'type' => 'client'
        ],
        [
            'nom' => 'OUEDRAOGO',
            'prenom' => 'innocent',
            'email' => 'innocent@gmail.com',
            'telephone' => '66748182',
            'adresse' => 'NIOKO2',
            'password' => Security::hashPassword('123456'),
            'type' => 'bailleur'
        ]
    ];
    
    foreach ($accounts as $account) {
        $sql = "INSERT INTO users (nom, prenom, email, telephone, adresse, password, type_utilisateur) 
                VALUES (:nom, :prenom, :email, :telephone, :adresse, :password, :type)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom' => $account['nom'],
            'prenom' => $account['prenom'],
            'email' => $account['email'],
            'telephone' => $account['telephone'],
            'adresse' => $account['adresse'],
            'password' => $account['password'],
            'type' => $account['type']
        ]);
        echo "✅ Compte créé : " . $account['email'] . " / " . str_replace('@', '', explode('@', $account['email'])[0]) . "123\n";
    }
    
    echo "\n🎉 Tous les comptes de test ont été créés avec BCRYPT !\n";
    echo "📋 Identifiants :\n";
    echo "   - Manager: manager1@immobilier.com / manager123\n";
    echo "   - Agent: agent@immobilier.com / agent123\n";
    echo "   - Client: nemata@gmail.com / client123\n";
    echo "   - Bailleur: innocent@gmail.com / bailleur123\n";
    
} catch(PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}