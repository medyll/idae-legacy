<?php 
//
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING & ~E_NOTICE);

// Set timezone if not already set
if (!ini_get('date.timezone')) {
    date_default_timezone_set('GMT');
}
/* ini_set('short_open_tag', 'On'); 
ini_set('error_reporting', 'E_ALL & ~E_DEPRECATED & ~E_STRICT');  */
ini_set('scream.enabled', true); 

// Define socket message size if not already defined
!defined('SOCKET_EMSGSIZE') && DEFINE('SOCKET_EMSGSIZE', 4000000);

// HTTP prefix (http or https)
$HTTP_PREFIX = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
setlocale(LC_CTYPE, 'fr_FR.UTF-8');
global $IMG_SIZE_ARR, $buildArr;

// Main path detection
$webDir = realpath(__DIR__);
$projectRoot = realpath($webDir . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;

// Host detection
$http_host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
$host      = explode(':', $http_host)[0];
$host_port = explode(':', $http_host)[1] ?? '';
$host_name = explode('.', $host)[0];
$host_parts = explode('.', $host);

// Only allow .lan hosts
if ('lan' === end($host_parts) || $host === 'localhost' || $host === '127.0.0.1') {
    DEFINE('ENVIRONEMENT', 'PREPROD');
} else  {
    DEFINE('ENVIRONEMENT', 'PROD');
}

// Load the correct hosts config file
$configFile = (defined('ENVIRONEMENT') && ENVIRONEMENT === 'PREPROD')
    ? __DIR__ . '/../config/lan-hosts.json'
    : __DIR__ . '/../config/prod-hosts.json';

$config = json_decode(file_get_contents($configFile), true);
if (!$config) {
    die('Erreur de lecture du fichier de configuration hosts.json');
}

// Check if host is configured
if (!isset($config['hosts'][$host])) {
    die("'[$host] Host non configuré dans $configFile'");
}
$hostConf = $config['hosts'][$host];

// Utility function to define constants if key exists
function define_if_exists($key, $array, $defineName) {
    if (isset($array[$key])) {
        DEFINE($defineName, $array[$key]);
    }
}
//
DEFINE('APPPATH', $projectRoot);
DEFINE('SITEPATH', $webDir . DIRECTORY_SEPARATOR);
// Define main paths and names
DEFINE('CUSTOMERPATH', SITEPATH);

// SESSION paths
DEFINE("SESSION_PATH", APPPATH . 'sessions' . DIRECTORY_SEPARATOR);
DEFINE("COOKIE_PATH", APPPATH . 'cookies' . DIRECTORY_SEPARATOR);
set_include_path(get_include_path() . PATH_SEPARATOR . SITEPATH);

DEFINE('DOCUMENTROOT', SITEPATH);
DEFINE('APPCONFDIR', SITEPATH . 'appconf' . DIRECTORY_SEPARATOR);

DEFINE('CONFINC', SITEPATH . 'conf.inc.php');
DEFINE('ACTIVEMODULEFILE', SITEPATH . 'services' . DIRECTORY_SEPARATOR . 'json_data_event.txt');

DEFINE('APPNAME', 'idae-' . $host_name . '-lan');
DEFINE('CUSTOMERNAME', $host_name);

DEFINE('APPBIN', SITEPATH . 'bin' . DIRECTORY_SEPARATOR);
DEFINE('APP_CONFIG_DIR', APPBIN . 'config' . DIRECTORY_SEPARATOR); 
DEFINE('APPMDL', SITEPATH . 'mdl' . DIRECTORY_SEPARATOR);
DEFINE('APPLESS', 'appcss' . DIRECTORY_SEPARATOR);
DEFINE('APPTPL', SITEPATH . 'tpl' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
DEFINE('APPBINTPL', SITEPATH . 'bin' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
DEFINE('PATHTMP', APPPATH . 'tmp' . DIRECTORY_SEPARATOR);
DEFINE('ADODBDIR', SITEPATH . 'adodb' . DIRECTORY_SEPARATOR);
DEFINE('REPFONCTIONS_APP', SITEPATH . 'appfunc' . DIRECTORY_SEPARATOR);
DEFINE('XMLDIR', SITEPATH . 'xmlfiles' . DIRECTORY_SEPARATOR);

DEFINE("APPCLASSES", SITEPATH . "appclasses" . DIRECTORY_SEPARATOR);
DEFINE("APPBINCLASSES", SITEPATH . "bin" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR);
DEFINE("APPCLASSES_APP", SITEPATH . "bin" . DIRECTORY_SEPARATOR . "classes_app" . DIRECTORY_SEPARATOR);
DEFINE("OLDAPPCLASSES", SITEPATH . "classes" . DIRECTORY_SEPARATOR);
DEFINE('REPFONCTIONS', SITEPATH . 'appfunc' . DIRECTORY_SEPARATOR);

// URLs and domain related constants
DEFINE('DOCUMENTDOMAIN', $host);
DEFINE('DOCUMENTDOMAINNOPORT', $host);
DEFINE('DOCUMENTDOMAINPORT', $host_port);
$host_port_part = !empty($host_port) ? ':' . $host_port : '';
DEFINE('HTTPCUSTOMERSITE', $HTTP_PREFIX . $host . $host_port_part . '/');
DEFINE('HTTPAPP', $HTTP_PREFIX . $host . $host_port_part . '/');
DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base' . DIRECTORY_SEPARATOR . CUSTOMERNAME . DIRECTORY_SEPARATOR);
DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . 'images_base/' . CUSTOMERNAME . '/');
DEFINE('SOCKETIO_PORT', 3005);
// Use host.docker.internal if running in Docker container (inferred from MONGO_HOST), otherwise default to DOCUMENTDOMAINNOPORT
DEFINE('SOCKET_HOST', getenv('MONGO_HOST') ? 'host.docker.internal' : DOCUMENTDOMAINNOPORT);

DEFINE('HTTPHOST', $HTTP_PREFIX . DOCUMENTDOMAIN . $host_port_part);
DEFINE('HTTPHOSTNOPORT', $HTTP_PREFIX . DOCUMENTDOMAINNOPORT);
DEFINE('NAMESITE', DOCUMENTDOMAIN);
DEFINE('MAINSITEHOST', HTTPHOST);
DEFINE('ACTIONMDL', $HTTP_PREFIX . DOCUMENTDOMAIN . '/mdl/');
DEFINE('HTTPCSS', $HTTP_PREFIX . DOCUMENTDOMAIN . '/css/');
DEFINE('HTTPMDL', $HTTP_PREFIX . DOCUMENTDOMAIN . '/mdl/');
DEFINE('HTTPJAVASCRIPT', $HTTP_PREFIX . DOCUMENTDOMAIN . '/javascript/');
DEFINE('HTTPIMAGES', $HTTP_PREFIX . DOCUMENTDOMAIN . '/images/');
DEFINE('ICONPATH', 'images/icones/');

DEFINE('PATH', DOCUMENTROOT);
DEFINE('PATHICC', DOCUMENTROOT . 'icc' . DIRECTORY_SEPARATOR);
DEFINE('APPROOT', DOCUMENTROOT);

// Host-specific config from JSON
DEFINE('APPSITE', $host);
DEFINE('BUSINESS', isset($hostConf['business']) ? $hostConf['business'] : '');

// SMTP config
if (isset($hostConf['smtp'])) {
    define_if_exists('host', $hostConf['smtp'], 'SMTPHOST');
    define_if_exists('user', $hostConf['smtp'], 'SMTPUSER');
    define_if_exists('email', $hostConf['smtp'], 'SMTPEMAIL');
    define_if_exists('pass', $hostConf['smtp'], 'SMTPPASS');
}
// MongoDB config
if (isset($hostConf['mdb'])) {
    // Allow runtime override (Docker or native)
    $envMongoHost = getenv('MONGO_HOST');
    $envMongoUser = getenv('MDB_USER');
    $envMongoPass = getenv('MDB_PASSWORD');
    $envMongoPrefix = getenv('MDB_PREFIX');
    if (!empty($envMongoHost)) {
        $hostConf['mdb']['host'] = $envMongoHost;
    }
    if (!empty($envMongoUser)) {
        $hostConf['mdb']['user'] = $envMongoUser;
    }
    if (!empty($envMongoPass)) {
        $hostConf['mdb']['password'] = $envMongoPass;
    }
    if (!empty($envMongoPrefix)) {
        $hostConf['mdb']['prefix'] = $envMongoPrefix;
    }
    // DEBUG PRINT: Show resolved MongoDB connection info
    if (!empty(getenv('DEBUG_DB'))) {
        // header('Content-Type: text/plain; charset=UTF-8');
        error_log("[DEBUG] MONGO_HOST (env): ".$envMongoHost);
        error_log("[DEBUG] MDB_HOST (final): ".$hostConf['mdb']['host']);
        error_log("[DEBUG] MDB_USER: ".$hostConf['mdb']['user']);
        error_log("[DEBUG] MDB_PASSWORD: ".($hostConf['mdb']['password'] ? '***' : ''));
        error_log("[DEBUG] MDB_PREFIX: ".$hostConf['mdb']['prefix']);
        error_log("[DEBUG] configFile: $configFile");
        error_log("[DEBUG] host: $host");
        // exit;
    }
    define_if_exists('host', $hostConf['mdb'], 'MDB_HOST');
    define_if_exists('user', $hostConf['mdb'], 'MDB_USER');
    define_if_exists('password', $hostConf['mdb'], 'MDB_PASSWORD');
    define_if_exists('prefix', $hostConf['mdb'], 'MDB_PREFIX');
    // Aliases pour compatibilité avec le nouveau driver
    if (!defined('MONGO_USER') && defined('MDB_USER')) define('MONGO_USER', MDB_USER);
    if (!defined('MONGO_PASS') && defined('MDB_PASSWORD')) define('MONGO_PASS', MDB_PASSWORD);
}
// SQL config
if (isset($hostConf['sql'])) {
    define_if_exists('host', $hostConf['sql'], 'SQL_HOST');
    define_if_exists('db', $hostConf['sql'], 'SQL_BDD');
    define_if_exists('user', $hostConf['sql'], 'SQL_USER');
    define_if_exists('password', $hostConf['sql'], 'SQL_PASSWORD');
}
// GED config
if (isset($hostConf['ged'])) {
    define_if_exists('host', $hostConf['ged'], 'SMTPHOSTGED');
    define_if_exists('user', $hostConf['ged'], 'SMTPUSERGED');
    define_if_exists('email', $hostConf['ged'], 'SMTPEMAILGED');
    define_if_exists('pass', $hostConf['ged'], 'SMTPPASSGED');
}

// --- Application includes and autoload ---

require_once(REPFONCTIONS_APP . "function_prod.php");
require_once(REPFONCTIONS_APP . "function.php");
require_once(REPFONCTIONS_APP . "function_site.php");

require_once(REPFONCTIONS_APP . "fonctionsDevis.php");
require_once(REPFONCTIONS_APP . "fonctionsJs.php");
include_once(REPFONCTIONS_APP . 'phpthumb/ThumbLib.inc.php');

// Load customer-specific functions if available
if (file_exists(APPMDL . 'customer/' . CUSTOMERNAME . '/function.php')) {
    include_once(APPMDL . 'customer/' . CUSTOMERNAME . '/function.php');
}

// Composer autoload
include_once('vendor/autoload.php');

// App skeleton
include_once(APPPATH . "web/appskel/skelStrap.php");

// Default image and build arrays
$IMG_SIZE_ARR = !empty($IMG_SIZE_ARR) ? $IMG_SIZE_ARR : ['square' => ['120', '120'], 'small' => ['210', '140'], 'large' => ['650', '430'], 'wallpaper' => ['1920', '1080']];
$buildArr     = !empty($buildArr) ? $buildArr : ['tiny' => ['100', '25'], 'squary' => ['68', '68'], 'largy' => ['325', '215'], 'wallpapery' => ['100', '25']];

// --- Autoloader ---
if (!function_exists("my_autoloader")) {
    function my_autoloader($class_name) {
        $dirs = array(
            APPCLASSES,
            APPCLASSES . '/appcommon/',
            APPBIN . '/classes/shared/',
            OLDAPPCLASSES,
            APPBINCLASSES
        );
        foreach($dirs as $directory){
            if(file_exists($directory.'Class' . $class_name . '.php')){
                require($directory.'Class' . $class_name . '.php');
                return true;
            }
        }
        $folder     = APPCLASSES;
        $class_name = ltrim($class_name, '\\');
        $fileName   = '';

        if ($lastNsPos = strripos($class_name, '\\')) {
            $namespace  = substr($class_name, 0, $lastNsPos);
            $class_name = substr($class_name, $lastNsPos + 1);
            $fileName   = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';
        if (file_exists($folder . $fileName)) require ($folder . $fileName);
        else{
            $name_class =  "\\$class_name";
            return new $name_class ;
        }
    }
    spl_autoload_register('my_autoloader');
}

// --- MongoDB Compatibility Layer (must load before ClassSession) ---
require_once(__DIR__ . '/appclasses/appcommon/MongoCompat.php');

// --- Session class include ---
include_once('appclasses/ClassSession.php');

// die("Configuration loaded. Session class included. You can now start the application.");

// --- Debug helper ---
if (!function_exists('myddeDebug')) {
    function myddeDebug($vars) {
        echo "<pre>";
        var_dump($vars);
        echo "</pre>";
    }
}
