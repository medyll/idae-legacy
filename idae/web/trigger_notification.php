<?php
/**
 * Script pour déclencher des notifications via PHP vers WebSocket
 * Usage: Appelé depuis test_notification.html ou directement via POST
 * 
 * Modified: 2026-02-07 - Script de test pour notifications
 */

require_once __DIR__ . '/conf.inc.php';
require_once CUSTOMERPATH . 'appskel/skelMdl.php';

session_start();

header('Content-Type: text/plain');

$action = $_POST['action'] ?? $_GET['action'] ?? 'help';

switch($action) {
    case 'test_notify':
        $message = "Test notification depuis PHP - " . date('H:i:s');
        $result = skelMdl::send_cmd('act_notify', ['msg' => $message]);
        
        if ($result) {
            echo "OK - Notification envoyée: $message";
        } else {
            echo "ERREUR - Échec envoi notification";
        }
        break;
        
    case 'test_notify_room':
        $room = $_POST['room'] ?? 'test-room';
        $message = "Test notification pour room '$room' - " . date('H:i:s');
        $result = skelMdl::send_cmd('act_notify', ['msg' => $message], $room);
        
        echo "Notification envoyée à room '$room': $message";
        break;
        
    case 'test_login_notification':
        // Simuler les notifications de login
        $agentName = $_POST['agent'] ?? 'Agent Test';
        
        skelMdl::send_cmd('act_notify', ['msg' => 'En ligne ' . $agentName]);
        skelMdl::send_cmd('act_notify', ['msg' => 'app_login_success']);
        
        echo "Notifications de login simulées pour: $agentName";
        break;
        
    case 'status':
        // Vérifier le statut de la configuration
        $status = [
            'SOCKETIO_PORT' => defined('SOCKETIO_PORT') ? SOCKETIO_PORT : 'NON DÉFINI',
            'SOCKET_HOST' => defined('SOCKET_HOST') ? SOCKET_HOST : 'NON DÉFINI',
            'DOCUMENTDOMAIN' => defined('DOCUMENTDOMAIN') ? DOCUMENTDOMAIN : 'NON DÉFINI',
            'Session ID' => session_id(),
            'Time' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($status, JSON_PRETTY_PRINT);
        break;
        
    case 'test_direct_socket':
        // Test direct de la connexion socket
        $host = defined('SOCKET_HOST') ? SOCKET_HOST : 'localhost';
        $port = SOCKETIO_PORT;
        
        $fp = @fsockopen($host, $port, $errno, $errstr, 5);
        if ($fp) {
            echo "OK - Connexion directe à $host:$port réussie";
            fclose($fp);
        } else {
            echo "ERREUR - Connexion directe échouée: $errstr ($errno)";
        }
        break;
        
    case 'help':
    default:
        echo "Actions disponibles:\n";
        echo "- test_notify: Envoie une notification générale\n";
        echo "- test_notify_room: Envoie une notification à une room spécifique\n";
        echo "- test_login_notification: Simule les notifications de login\n";
        echo "- status: Affiche la configuration actuelle\n";
        echo "- test_direct_socket: Teste la connexion réseau directe\n";
        echo "\nUsage: POST/GET avec param 'action'\n";
        break;
}
?>