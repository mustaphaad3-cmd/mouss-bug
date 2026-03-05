<?php
// config.php

// Configuration Telegram
define('TELEGRAM_BOT_TOKEN', 'TON_TOKEN_TELEGRAM'); // Obtenir via @BotFather
define('TELEGRAM_CHAT_ID', 'TON_CHAT_ID'); // Obtenir via @userinfobot

// Configuration email
define('EMAIL_RECIPIENT', 'tonemail@gmail.com');
define('EMAIL_SUBJECT_PREFIX', '[DATA COLLECTOR]');

// Configuration base de données (optionnel)
define('DB_HOST', 'localhost');
define('DB_NAME', 'collector_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Fonction pour créer la base de données
function initDatabase() {
    try {
        $pdo = new PDO("mysql:host=".DB_HOST, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Créer la base
        $pdo->exec("CREATE DATABASE IF NOT EXISTS ".DB_NAME);
        $pdo->exec("USE ".DB_NAME);
        
        // Créer la table
        $sql = "CREATE TABLE IF NOT EXISTS collected_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(255),
            email VARCHAR(255),
            telephone VARCHAR(50),
            adresse TEXT,
            ville VARCHAR(255),
            cp VARCHAR(20),
            telephone_type VARCHAR(50),
            modele VARCHAR(255),
            operateur VARCHAR(255),
            whatsapp VARCHAR(255),
            snapchat VARCHAR(255),
            instagram VARCHAR(255),
            facebook VARCHAR(255),
            notes TEXT,
            ip VARCHAR(50),
            user_agent TEXT,
            received_at DATETIME
        )";
        
        $pdo->exec($sql);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}
?>