<?php
/**
 * Test script to diagnose WebSocket notification system
 * Usage: Access via browser to test complete flow
 * 
 * Modified: 2026-02-07 - Diagnostic pour système de notification
 */

require_once __DIR__ . '/conf.inc.php';
require_once CUSTOMERPATH . 'appskel/skelMdl.php';

session_start();

echo "<h2>Diagnostic Système de Notification</h2>";
echo "<style>body { font-family: monospace; } .ok { color: green; } .error { color: red; } .info { color: blue; }</style>";

// 1. Test Configuration
echo "<h3>1. Configuration WebSocket</h3>";
echo "<div class='info'>SOCKETIO_PORT: " . (defined('SOCKETIO_PORT') ? SOCKETIO_PORT : 'NON DÉFINI') . "</div>";
echo "<div class='info'>SOCKET_HOST: " . (defined('SOCKET_HOST') ? SOCKET_HOST : 'NON DÉFINI') . "</div>";
echo "<div class='info'>DOCUMENTDOMAIN: " . (defined('DOCUMENTDOMAIN') ? DOCUMENTDOMAIN : 'NON DÉFINI') . "</div>";
echo "<div class='info'>Environment: " . (defined('ENVIRONEMENT') ? ENVIRONEMENT : 'NON DÉFINI') . "</div>";
echo "<div class='info'>MONGO_HOST env: " . (getenv('MONGO_HOST') ?: 'NON DÉFINI') . "</div>";

// 2. Test Session PHP
echo "<h3>2. Session PHP</h3>";
echo "<div class='info'>Session ID: " . session_id() . "</div>";
echo "<div class='info'>Session Name: " . session_name() . "</div>";

// 3. Test Communication avec Node.js via doSocket
echo "<h3>3. Test Communication PHP → Node.js</h3>";
$socketHost = defined('SOCKET_HOST') ? SOCKET_HOST : (defined('HTTPHOSTNOPORT') ? HTTPHOSTNOPORT : 'localhost');
$socketUrl = $socketHost . ':' . SOCKETIO_PORT . '/postReload';
echo "<div class='info'>URL de test: " . $socketUrl . "</div>";

// Test de base: ping le serveur Node.js
$testData = [
    'cmd' => 'act_notify',
    'vars' => [
        'msg' => 'Test notification depuis diagnostic_notification.php à ' . date('H:i:s')
    ],
    'DOCUMENTDOMAIN' => DOCUMENTDOMAIN,
    'PHPSESSID' => session_id()
];

echo "<div class='info'>Données envoyées: <pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre></div>";

$result = skelMdl::doSocket($socketUrl, $testData);
if ($result) {
    echo "<div class='ok'>✓ Communication PHP → Node.js réussie</div>";
} else {
    echo "<div class='error'>✗ Échec de la communication PHP → Node.js</div>";
}

// 4. Test direct de connexion réseau
echo "<h3>4. Test Réseau Direct</h3>";
$host = defined('SOCKET_HOST') ? SOCKET_HOST : 'localhost';
$port = SOCKETIO_PORT;

$fp = @fsockopen($host, $port, $errno, $errstr, 5);
if ($fp) {
    echo "<div class='ok'>✓ Connexion réseau directe sur $host:$port réussie</div>";
    fclose($fp);
} else {
    echo "<div class='error'>✗ Connexion réseau directe échouée: $errstr ($errno)</div>";
}

// 4.5. Test des cookies
echo "<h3>4.5. Test Cookies</h3>";
echo "<div class='info'>Cookies PHP disponibles: <pre>" . print_r($_COOKIE, true) . "</pre></div>";
echo "<div class='info'>Cookie PHPSESSID: " . (isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : 'NON DÉFINI') . "</div>";

// Force définir le cookie pour le navigateur si pas encore fait
if (!isset($_COOKIE['PHPSESSID'])) {
    setcookie('PHPSESSID', session_id(), [
        'path' => '/',
        'domain' => '',  // Permet localhost
        'secure' => false, // HTTP ok en développement
        'httponly' => false, // Accessible via JavaScript
        'samesite' => 'Lax'
    ]);
    echo "<div class='info'>✓ Cookie PHPSESSID défini pour le navigateur</div>";
}

// 5. Affichage JavaScript pour test côté client
?>

<h3>5. Test Côté Client (JavaScript)</h3>
<div id="client-status"></div>
<div id="socket-logs"></div>
<button onclick="testClientSocket()">Tester Connexion WebSocket</button>
<button onclick="testGrant()">Tester GrantIn</button>

<script>
// Import Socket.IO depuis le CDN pour le test
const script = document.createElement('script');
script.src = 'https://cdn.socket.io/4.0.0/socket.io.min.js';
script.onload = function() {
    testClientSocket();
};
document.head.appendChild(script);

function log(msg, level = 'info') {
    const div = document.createElement('div');
    div.className = level;
    div.textContent = new Date().toLocaleTimeString() + ': ' + msg;
    document.getElementById('socket-logs').appendChild(div);    
}

function testClientSocket() {
    if (typeof io === 'undefined') {
        log('Socket.IO pas encore chargé, rechargez la page', 'error');
        return;
    }
    
    // Même logique que app_socket.js : same-origin, Apache reverse-proxy
    // /socket.io/ vers le serveur node du même conteneur.
    var socketUrl = document.location.origin;
    log('Tentative de connexion à: ' + socketUrl);
    
    const socket = io.connect(socketUrl, {
        'sync disconnect on unload': true
    });
    
    socket.on('connect', function() {
        log('✓ Connexion WebSocket établie', 'ok');
        document.getElementById('client-status').innerHTML = '<div class="ok">WebSocket connecté: ' + socket.id + '</div>';
    });
    
    socket.on('connect_error', function(error) {
        log('✗ Erreur de connexion: ' + error.message, 'error');
        document.getElementById('client-status').innerHTML = '<div class="error">Erreur: ' + error.message + '</div>';
    });
    
    socket.on('receive_cmd', function(data) {
        log('Commande reçue: ' + JSON.stringify(data), 'ok');
        if (data.cmd === 'act_notify') {
            log('🔔 Notification reçue: ' + (data.vars?.msg || 'pas de message'), 'ok');
        }
    });
    
    socket.on('notify', function(data) {
        log('Événement notify reçu: ' + JSON.stringify(data), 'ok');
    });
    
    // Sauvegarder pour les autres tests
    window.testSocket = socket;
}

function testGrant() {
    if (!window.testSocket || !window.testSocket.connected) {
        log('WebSocket pas connecté', 'error');
        return;
    }
    
    const grantData = {
        DOCUMENTDOMAIN: '<?= DOCUMENTDOMAIN ?>',
        IDAGENT: 1,
        SESSID: 1, 
        PHPSESSID: '<?= session_id() ?>'
    };
    
    log('Envoi grantIn: ' + JSON.stringify(grantData));
    
    window.testSocket.emit('grantIn', grantData, function(response) {
        log('Réponse grantIn: ' + response, 'ok');
    });
}
</script>

<h3>6. Informations Environnement</h3>
<div class='info'>User Agent: <?= $_SERVER['HTTP_USER_AGENT'] ?? 'N/A' ?></div>
<div class='info'>HTTP Host: <?= $_SERVER['HTTP_HOST'] ?? 'N/A' ?></div>
<div class='info'>Document Domain JS: <script>document.write(document.domain)</script></div>
<div class='info'>Protocol: <script>document.write(document.location.protocol)</script></div>