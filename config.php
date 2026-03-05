<?php
// save_data.php

header('Content-Type: application/json');

// Activer les logs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration
$data_file = 'collected_data.json';
$log_file = 'log.txt';

// Fonction pour logger
function logMessage($message) {
    global $log_file;
    $date = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$date] $message\n", FILE_APPEND);
}

// Récupérer les données
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    logMessage("Erreur: Données invalides");
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Ajouter l'IP et la date
$input['ip'] = $_SERVER['REMOTE_ADDR'];
$input['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
$input['received_at'] = date('Y-m-d H:i:s');

// Sauvegarder dans le fichier
$existing_data = [];
if (file_exists($data_file)) {
    $existing_data = json_decode(file_get_contents($data_file), true) ?? [];
}

$existing_data[] = $input;
file_put_contents($data_file, json_encode($existing_data, JSON_PRETTY_PRINT));

// Envoyer un email (optionnel)
$to = "tonemail@gmail.com";
$subject = "Nouvelles données collectées";
$message = "Nouvelles données reçues :\n\n" . print_r($input, true);
$headers = "From: collector@localhost";

mail($to, $subject, $message, $headers);

logMessage("Données sauvegardées pour: " . $input['nom']);

echo json_encode(['success' => true, 'message' => 'Données sauvegardées']);
?>