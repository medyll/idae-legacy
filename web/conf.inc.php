<?php
//header('Content-Type: text/html; charset=UTF-8');
// header('Pragma: public');
// header('Pragma: public');
//header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));
// session_start();
//
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if (!ini_get('date.timezone')) {
    date_default_timezone_set('GMT');
}
ini_set('short_open_tag', 'On');
ini_set('scream.enabled', false);

!defined('SOCKET_EMSGSIZE') && DEFINE('SOCKET_EMSGSIZE', 4000000);

$HTTP_PREFIX = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
//
// ini_set('session.save_path','/var/www/idaertys.mydde.fr/tmp/') ;
//
setlocale(LC_CTYPE, 'fr_FR.UTF-8');
//
global $IMG_SIZE_ARR, $buildArr;
//
$host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
$host_name = explode('.', $_SERVER['HTTP_HOST'])[0];
//
if ('lan' === end(explode('.', $_SERVER['HTTP_HOST']))) {
    ini_set('display_errors', 55);
    /*		DEFINE('ENVIRONEMENT', 'PREPROD');
            DEFINE('SITEPATH', 'D:\boulot\UwAmp\www\idae.preprod.lan\web\\');
            DEFINE('APPPATH', 'D:\boulot\UwAmp\www\idae.preprod.lan\\');*/
    include_once('conf.lan.inc.php');

    return;
} else {
    if (strpos($_SERVER['HTTP_HOST'], 'preprod') === false) {
        ini_set('display_errors', 0);
        DEFINE('ENVIRONEMENT', 'PROD');
        DEFINE('SITEPATH', '/var/www/idaertys.mydde.fr/web/');
        DEFINE('APPPATH', '/var/www/idaertys.mydde.fr/');
    } else {
        ini_set('display_errors', 55);
        DEFINE('ENVIRONEMENT', 'PREPROD');
        DEFINE('SITEPATH', '/var/www/idaertys_preprod.mydde.fr/web/');
        DEFINE('APPPATH', '/var/www/idaertys_preprod.mydde.fr/');
    }
}
// SESSION
DEFINE("SESSION_PATH", APPPATH . 'sessions/');
DEFINE("COOKIE_PATH", APPPATH . '/cookies/');
set_include_path(get_include_path() . ':' . APPPATH . '/web');
//
DEFINE('DOCUMENTROOT', APPPATH . '/web/');
DEFINE('APPCONFDIR', APPPATH . '/web/appconf/');

DEFINE('CONFINC', APPPATH . '/web/conf.inc.php');
DEFINE('ACTIVEMODULEFILE', APPPATH . '/web/services/json_data_event.txt');
//
DEFINE('APPBIN', APPPATH . 'web/bin/');
DEFINE('APP_CONFIG_DIR', APPBIN . 'config/'); // auto creation of metier
DEFINE('APPMDL', APPPATH . 'web/mdl/');
DEFINE('APPLESS', 'appcss/');
DEFINE('APPTPL', APPPATH . 'web/tpl/app/');
DEFINE('APPBINTPL', APPPATH . 'web/bin/templates/app/');
DEFINE('PATHTMP', APPPATH . '/tmp/');
DEFINE('ADODBDIR', APPPATH . '/web/adodb/');
DEFINE('REPFONCTIONS_APP', APPPATH . '/web/appfunc/');
DEFINE('XMLDIR', APPPATH . 'web/xmlfiles/');
//
DEFINE("APPCLASSES", APPPATH . "web/appclasses/");
DEFINE("APPBINCLASSES", APPPATH . "web/bin/classes/");
DEFINE("APPCLASSES_APP", APPPATH . "web/bin/classes_app/");
DEFINE("OLDAPPCLASSES", APPPATH . "web/classes/");
DEFINE('REPFONCTIONS', APPPATH . 'appfunc/');

// prod / last / preprod

switch ($host) {
    case "appmaw.mydde.fr":
        DEFINE('BUSINESS', 'cruise');
        DEFINE('APPSITE', 'croisieres-maw.fr');

        DEFINE('APPNAME', 'Idae maw');
        DEFINE('CUSTOMERNAME', 'maw');
        DEFINE('DOCUMENTDOMAIN', 'appmaw.mydde.fr');
        DEFINE('DOCUMENTDOMAINNOPORT', 'appmaw.mydde.fr');
        DEFINE('DOCUMENTDOMAINPORT', '8000');
        DEFINE('HTTPCUSTOMERSITE', 'http://croisieres-maw.fr/');
        DEFINE('CUSTOMERPATH', '/var/www/croisieres-maw.fr/web/');

        DEFINE('HTTPAPP', 'http://appmaw.mydde.fr/');
        //
        DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/' . CUSTOMERNAME . '/');
        DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/' . CUSTOMERNAME . '/');
        //
        DEFINE('SOCKETIO_PORT', 3005);
        //
        DEFINE("SQL_HOST", "localhost");
        DEFINE("SQL_BDD", "crm_general_new");
        DEFINE("SQL_USER", "root");
        DEFINE("SQL_PASSWORD", "redPoi654pied");
        //
        DEFINE("MDB_HOST", "127.0.0.1");
        DEFINE("MDB_USER", "admin");
        DEFINE("MDB_PASSWORD", "gwetme2011");
        DEFINE("MDB_PREFIX", "maw_");
        //
        DEFINE('SMTPHOST', 'localhost');
        DEFINE('SMTPUSER', 'reservations@croisieres-maw.fr');
        DEFINE('SMTPPASS', 'mexicoMAW2016');
        //
        DEFINE('HTTPEXTERNALCUSTOMERSITE', 'https://maw.lan');
        break;
    case "appcrfr.mydde.fr":
        DEFINE('BUSINESS', 'cruise');
        DEFINE('APPSITE', 'croisierefr.com');

        DEFINE('APPNAME', 'appcrfr');
        DEFINE('CUSTOMERNAME', 'croisierefr');

        DEFINE('DOCUMENTDOMAIN', 'appcrfr.mydde.fr');
        DEFINE('DOCUMENTDOMAINNOPORT', 'appcrfr.mydde.fr');
        DEFINE('DOCUMENTDOMAINPORT', '80');

        DEFINE('HTTPCUSTOMERSITE', 'http://croisierefr.com/');
        DEFINE('CUSTOMERPATH', '/var/www/croisierefr.com/web/');
        //
        DEFINE('HTTPAPP', 'http://appcrfr.mydde.fr/');
        //
        DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/' . CUSTOMERNAME . '/');
        DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/' . CUSTOMERNAME . '/');
        //
        DEFINE('SOCKETIO_PORT', 3005);
        //
        DEFINE("SQL_HOST", "localhost");
        DEFINE("SQL_BDD", "crm_general_new");
        DEFINE("SQL_USER", "root");
        DEFINE("SQL_PASSWORD", "redPoi654pied");
        //
        DEFINE("MDB_HOST", "127.0.0.1");
        DEFINE("MDB_USER", "admin");
        DEFINE("MDB_PASSWORD", "gwetme2011");
        DEFINE("MDB_PREFIX", "crfr_");
        //
        DEFINE('SMTPHOST', 'localhost');
        DEFINE('SMTPUSER', 'reservations@appcrfr.fr');
        DEFINE('SMTPPASS', '123');
        //
        // TAILLE DES IMAGES !!!
        $IMG_SIZE_ARR = ['square' => ['120', '120'], 'tiny' => ['135', '68'], 'small' => ['250', '120'], 'large' => ['650', '215'], 'long' => ['1000', '245'], 'wallpaper' => ['1920', '1080']];
        $buildArr = ['tinyy' => [50, 25],
            'smally' => [68, 68],
            'longy' => [375, 60],
            'largy' => [325, 215],
            'largey' => [325, 215],
            'wallpapery' => ['100', '25']
        ];

        break;
    case "appmaw-idaertys-preprod.mydde.fr":

        DEFINE('BUSINESS', 'cruise');
        DEFINE('APPSITE', 'croisieres-maw.fr');

        DEFINE('APPNAME', 'Idae maw');
        DEFINE('CUSTOMERNAME', 'maw');
        DEFINE('DOCUMENTDOMAIN', 'appmaw-idaertys-preprod.mydde.fr');
        DEFINE('DOCUMENTDOMAINNOPORT', 'appmaw-idaertys-preprod.mydde.fr');
        DEFINE('DOCUMENTDOMAINPORT', '80');
        DEFINE('HTTPCUSTOMERSITE', 'http://croisieres-maw.fr/');
        DEFINE('CUSTOMERPATH', '/var/www/croisieres-maw.fr/web/');

        DEFINE('HTTPAPP', 'http://appmaw-idaertys-preprod.mydde.fr/');
        //
        DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/' . CUSTOMERNAME . '/');
        DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/' . CUSTOMERNAME . '/');
        //
        DEFINE('SOCKETIO_PORT', 3006);
        //
        DEFINE("SQL_HOST", "localhost");
        DEFINE("SQL_BDD", "crm_general_new");
        DEFINE("SQL_USER", "root");
        DEFINE("SQL_PASSWORD", "redPoi654pied");
        //
        DEFINE("MDB_HOST", "127.0.0.1");
        DEFINE("MDB_USER", "admin");
        DEFINE("MDB_PASSWORD", "gwetme2011");
        DEFINE("MDB_PREFIX", "maw_");
        //
        DEFINE('SMTPHOST', 'localhost');
        DEFINE('SMTPUSER', 'reservations@croisieres-maw.fr');
        DEFINE('SMTPPASS', 'mexicoMAW2016');
        //
        ini_set('display_errors', 0);
        DEFINE('HTTPEXTERNALCUSTOMERSITE', 'https://maw.lan');
        break;
    case "appblog.idaertys-preprod.mydde.fr":
        ini_set('display_errors', 55);
        DEFINE('BUSINESS', 'blog');// DEFINE('BUSINESS', 'commercial');
        DEFINE('APPSITE', 'croisierefr.com');

        DEFINE('APPNAME', 'appblog');
        DEFINE('CUSTOMERNAME', 'appblog');
        DEFINE('DOCUMENTDOMAIN', 'appblog.idaertys-preprod.mydde.fr');
        DEFINE('DOCUMENTDOMAINNOPORT', 'appblog.idaertys-preprod.mydde.fr');
        DEFINE('DOCUMENTDOMAINPORT', '80');
        // WEBSITE
        DEFINE('HTTPCUSTOMERSITE', 'http://appblog.idaertys-preprod.mydde.fr/');
        DEFINE('CUSTOMERPATH', '/var/www/appblog.idaertys-preprod.mydde.fr/web/');
        //
        DEFINE('HTTPAPP', 'http://appblog.idaertys-preprod.mydde.fr/');
        //
        DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/' . CUSTOMERNAME . '/');
        DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/' . CUSTOMERNAME . '/');
        //
        DEFINE('SOCKETIO_PORT', 3006);
        //
        DEFINE("SQL_HOST", "localhost");
        DEFINE("SQL_BDD", "crm_general_new");
        DEFINE("SQL_USER", "root");
        DEFINE("SQL_PASSWORD", "redPoi654pied");
        //
        DEFINE("MDB_HOST", "127.0.0.1");
        DEFINE("MDB_USER", "admin");
        DEFINE("MDB_PASSWORD", "gwetme2011");
        DEFINE("MDB_PREFIX", "appblog_");
        //
        DEFINE('SMTPHOST', 'localhost');
        DEFINE('SMTPUSER', 'reservations@appcrfr.fr');
        DEFINE('SMTPPASS', '123');
        //
        // TAILLE DES IMAGES !!!
        $IMG_SIZE_ARR = ['square' => ['120', '120'], 'tiny' => ['135', '68'], 'small' => ['250', '120'], 'large' => ['650', '215'], 'long' => ['1000', '245'], 'wallpaper' => ['1920', '1080']];
        $buildArr = ['tinyy' => [50, 25],
            'smally' => [68, 68],
            'longy' => [375, 60],
            'largy' => [325, 215],
            'largey' => [325, 215],
            'wallpapery' => [100, 25]
        ];

        break;
    case "appcrfr.idaertys-preprod.mydde.fr":
        DEFINE('BUSINESS', 'cruise');
        DEFINE('APPSITE', 'croisierefr.com');

        DEFINE('APPNAME', 'appcrfr');
        DEFINE('CUSTOMERNAME', 'croisierefr');
        DEFINE('DOCUMENTDOMAIN', 'appcrfr-idaertys-preprod.mydde.fr');
        DEFINE('DOCUMENTDOMAINNOPORT', 'appcrfr-idaertys-preprod.mydde.fr');
        DEFINE('DOCUMENTDOMAINPORT', '80');
        // WEBSITE
        DEFINE('HTTPCUSTOMERSITE', 'http://appcrfr-idaertys-preprod.mydde.fr/');
        DEFINE('CUSTOMERPATH', '/var/www/idaertys_preprod.mydde.fr/web/');
        //
        DEFINE('HTTPAPP', 'http://appcrfr-idaertys-preprod.mydde.fr/');
        //
        DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/' . CUSTOMERNAME . '/');
        DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/' . CUSTOMERNAME . '/');
        //
        DEFINE('SOCKETIO_PORT', 3006);
        //
        DEFINE("SQL_HOST", "localhost");
        DEFINE("SQL_BDD", "crm_general_new");
        DEFINE("SQL_USER", "root");
        DEFINE("SQL_PASSWORD", "redPoi654pied");
        //
        DEFINE("MDB_HOST", "127.0.0.1");
        DEFINE("MDB_USER", "admin");
        DEFINE("MDB_PASSWORD", "gwetme2011");
        DEFINE("MDB_PREFIX", "crfr_");
        //
        DEFINE('SMTPHOST', 'localhost');
        DEFINE('SMTPUSER', 'reservations@appcrfr.fr');
        DEFINE('SMTPPASS', '123');
        //
        // TAILLE DES IMAGES !!!
        $IMG_SIZE_ARR = ['square' => ['120', '120'], 'tiny' => ['135', '68'], 'small' => ['250', '120'], 'large' => ['650', '215'], 'long' => ['1000', '245'], 'wallpaper' => ['1920', '1080']];
        $buildArr = ['tinyy' => [50, 25],
            'smally' => [68, 68],
            'longy' => [375, 60],
            'largy' => [325, 215],
            'largey' => [325, 215],
            'wallpapery' => [100, 25]
        ];

        break;
    case "idaertys.mydde.fr":
        die();
        DEFINE('BUSINESS', 'commercial');

        DEFINE('APPNAME', 'Idae');
        DEFINE('CUSTOMERNAME', 'leasys');
        DEFINE('DOCUMENTDOMAIN', 'idaertys.mydde.fr');
        DEFINE('DOCUMENTDOMAINNOPORT', 'idaertys.mydde.fr');
        DEFINE('DOCUMENTDOMAINPORT', '');
        DEFINE('HTTPCUSTOMERSITE', $HTTP_PREFIX . 'idaertys.mydde.fr/');
        DEFINE('CUSTOMERPATH', '/var/www/idaertys.mydde.fr/web/');
        //
        DEFINE('HTTPAPP', 'http://idaertys.mydde.fr/');
        //
        DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/' . CUSTOMERNAME . '/');
        DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/' . CUSTOMERNAME . '/');
        //
        DEFINE('SOCKETIO_PORT', 3005);
        //
        DEFINE("SQL_HOST", "localhost");
        DEFINE("SQL_BDD", "crm_general_new");
        DEFINE("SQL_USER", "root");
        DEFINE("SQL_PASSWORD", "redPoi654pied");
        //
        DEFINE("MDB_HOST", "127.0.0.1");
        DEFINE("MDB_USER", "admin");
        DEFINE("MDB_PASSWORD", "gwetme2011");
        DEFINE("MDB_PREFIX", "idaenext_");
        //
        DEFINE('SMTPHOST', 'mail.mydde.fr');
        DEFINE('SMTPUSER', 'ged.idae@mydde.fr'); //

        DEFINE('SMTPEMAIL', 'ged.idae@mydde.fr');
        DEFINE('SMTPPASS', 'malaterre654');

        DEFINE('SMTPHOSTGED', 'mail.mydde.fr');
        DEFINE('SMTPUSERGED', 'ged.idae@mydde.fr'); //
        DEFINE('SMTPEMAILGED', 'ged.idae@mydde.fr');
        DEFINE('SMTPPASSGED', 'malaterre654');

        //
        break;
    case "idaertys-preprod.mydde.fr":

        DEFINE('BUSINESS', 'commercial');// DEFINE('BUSINESS', 'commercial');

        DEFINE('APPNAME', 'idaertys-preprod');
        DEFINE('CUSTOMERNAME', 'leasys');
        DEFINE('DOCUMENTDOMAIN', 'idaertys-preprod.mydde.fr');
        DEFINE('DOCUMENTDOMAINNOPORT', 'idaertys-preprod.mydde.fr');
        DEFINE('DOCUMENTDOMAINPORT', '');
        DEFINE('HTTPCUSTOMERSITE', $HTTP_PREFIX . 'idaertys-preprod.mydde.fr/');
        DEFINE('CUSTOMERPATH', '/var/www/idaertys_preprod.mydde.fr/web/');
        //
        DEFINE('HTTPAPP', $HTTP_PREFIX . 'idaertys-preprod.mydde.fr/');
        //
        DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/' . CUSTOMERNAME . '/');
        DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/' . CUSTOMERNAME . '/');
        //
        DEFINE('SOCKETIO_PORT', 3006);
        //
        DEFINE("SQL_HOST", "localhost");
        DEFINE("SQL_BDD", "crm_general_new");
        DEFINE("SQL_USER", "root");
        DEFINE("SQL_PASSWORD", "redPoi654pied");
        //
        DEFINE("MDB_HOST", "127.0.0.1");
        DEFINE("MDB_USER", "admin");
        DEFINE("MDB_PASSWORD", "gwetme2011");
        DEFINE("MDB_PREFIX", "idaenext_"); // crfr_ // maw_ // idaenext_
        //
        DEFINE('SMTPHOST', 'mail.mydde.fr');
        DEFINE('SMTPUSER', 'ged.idae@mydde.fr'); //
        DEFINE('SMTPEMAIL', 'ged.idae@mydde.fr');
        DEFINE('SMTPPASS', 'malaterre654');

        DEFINE('SMTPHOSTGED', 'mail.mydde.fr');
        DEFINE('SMTPUSERGED', 'ged.idae@mydde.fr'); //
        DEFINE('SMTPEMAILGED', 'ged.idae@mydde.fr');
        DEFINE('SMTPPASSGED', 'malaterre654');

        //
        break;
    case "tactac_idae.preprod.mydde.fr":
        die();
        DEFINE('BUSINESS', 'foodlivery');// DEFINE('BUSINESS', 'commercial');

        DEFINE('APPNAME', 'tactac-preprod');
        DEFINE('CUSTOMERNAME', 'tactac');
        DEFINE('DOCUMENTDOMAIN', 'tactac_idae.preprod.mydde.fr');
        DEFINE('DOCUMENTDOMAINNOPORT', 'tactac_idae.preprod.mydde.fr');
        DEFINE('DOCUMENTDOMAINPORT', '');
        DEFINE('HTTPCUSTOMERSITE', $HTTP_PREFIX . 'tactac_idae.preprod.mydde.fr/');
        DEFINE('CUSTOMERPATH', '/var/www/idaertys_preprod.mydde.fr/web/');
        //
        DEFINE('HTTPAPP', $HTTP_PREFIX . 'idaertys-preprod.mydde.fr/');
        //
        DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/' . CUSTOMERNAME . '/');
        DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/' . CUSTOMERNAME . '/');
        //
        DEFINE('SOCKETIO_PORT', 3006);
        //
        DEFINE("SQL_HOST", "localhost");
        DEFINE("SQL_BDD", "crm_general_new");
        DEFINE("SQL_USER", "root");
        DEFINE("SQL_PASSWORD", "redPoi654pied");
        //
        DEFINE("MDB_HOST", "127.0.0.1");
        DEFINE("MDB_USER", "admin");
        DEFINE("MDB_PASSWORD", "gwetme2011");
        DEFINE("MDB_PREFIX", "tactac_"); // crfr_ // maw_ // idaenext_
        //
        DEFINE('SMTPHOST', 'mail.mydde.fr');
        DEFINE('SMTPUSER', 'ged.idae@mydde.fr'); //
        DEFINE('SMTPEMAIL', 'ged.idae@mydde.fr');
        DEFINE('SMTPPASS', 'malaterre654');

        DEFINE('SMTPHOSTGED', 'mail.mydde.fr');
        DEFINE('SMTPUSERGED', 'ged.idae@mydde.fr'); //
        DEFINE('SMTPEMAILGED', 'ged.idae@mydde.fr');
        DEFINE('SMTPPASSGED', 'malaterre654');

        //
        // TAILLE DES IMAGES !!!
        $IMG_SIZE_ARR = ['square' => ['150', '150'], 'tiny' => ['150', '70'], 'small' => ['300', '200'], 'long' => ['1100', '100'], 'large' => ['1100', '350'], 'wallpaper' => ['1920', '1080']];
        $buildArr = ['tinyy' => [50, 25],
            'tiny' => [150, 70],
            'smally' => [68, 68],
            'squary' => [70, 70],
            'largy' => [325, 215],
            'largey' => [325, 215],
            'wallpapery' => [100, 25]
        ];

        break;
    case "idaertys.mydde.fr:8000":
        die();
        DEFINE('APPNAME', 'Idaertys');
        DEFINE('CUSTOMERNAME', 'leasys');
        DEFINE('DOCUMENTDOMAIN', 'idaertys.mydde.fr:8000');
        DEFINE('DOCUMENTDOMAINNOPORT', 'idaertys.mydde.fr');
        DEFINE('DOCUMENTDOMAINPORT', '8000');
        DEFINE('HTTPCUSTOMERSITE', 'http://idaertys.mydde.fr:8000/');
        DEFINE('CUSTOMERPATH', '/var/www/idaertys.mydde.fr/web/');
        //
        DEFINE('HTTPAPP', 'http://idaertys.mydde.fr:8000/');
        //
        DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/');
        DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/');
        //
        DEFINE('SOCKETIO_PORT', 3005);
        //
        DEFINE("SQL_HOST", "localhost");
        DEFINE("SQL_BDD", "crm_general_new");
        DEFINE("SQL_USER", "root");
        DEFINE("SQL_PASSWORD", "redPoi654pied");
        //
        DEFINE("MDB_HOST", "127.0.0.1");
        DEFINE("MDB_USER", "admin");
        DEFINE("MDB_PASSWORD", "gwetme2011");
        DEFINE("MDB_PREFIX", "idaenext_");
        //
        DEFINE('SMTPHOST', 'remote.leasys.fr');
        DEFINE('SMTPUSER', 'mlebrun@leasys.fr');
        DEFINE('SMTPPASS', 'malaterre654');
        //
        break;
    case "localhost":
        DEFINE('SITEPATH', 'C:\xampp\htdocs\idaertys\web\\');
        DEFINE('APPPATH', 'C:\xampp\htdocs\idaertys\\');
        DEFINE('BUSINESS', 'commercial');

        DEFINE('APPNAME', 'Idaertys');
        DEFINE('CUSTOMERNAME', 'leasys');
        DEFINE('DOCUMENTDOMAIN', 'localhost\idaertys\web'); // with port number
        DEFINE('DOCUMENTDOMAINNOPORT', 'localhost\idaertys\web');
        DEFINE('DOCUMENTDOMAINPORT', '8000');
        DEFINE('HTTPCUSTOMERSITE', 'http://localhost/');
        DEFINE('CUSTOMERPATH', 'C:\xampp\htdocs\idaertys\web\\');//
        DEFINE('HTTPAPP', 'http://localhost/');
        //
        DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/');
        DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/');
        //
        DEFINE('SOCKETIO_PORT', 3005);
        //
        DEFINE("SQL_HOST", "localhost");
        DEFINE("SQL_BDD", "crm_general_new");
        DEFINE("SQL_USER", "root");
        DEFINE("SQL_PASSWORD", "redPoi654pied");
        //
        DEFINE("MDB_HOST", "127.0.0.1");
        DEFINE("MDB_USER", "admin");
        DEFINE("MDB_PASSWORD", "gwetme2011");
        DEFINE("MDB_PREFIX", "idaenext_");
        //
        DEFINE('SMTPHOST', 'remote.leasys.fr');
        DEFINE('SMTPUSER', 'mlebrun@leasys.fr');
        DEFINE('SMTPPASS', 'malaterre654');
        //
        break;
    case "preprod.mydde.fr":
        echo "malawi";
        exit;
        break;
    default :
        DEFINE('BUSINESS', 'notset');
        DEFINE('APPNAME', 'Idaertys');
        DEFINE('DOCUMENTDOMAIN', 'idaertys.mydde.fr:8000');
        DEFINE('DOCUMENTDOMAINNOPORT', 'idaertys.mydde.fr');
        DEFINE('DOCUMENTDOMAINPORT', '8000');
        DEFINE('HTTPCUSTOMERSITE', 'http://idaertys.mydde.fr:8000/');
        DEFINE('CUSTOMERPATH', '/var/www/idaertys.mydde.fr/web/');
        //
        DEFINE('HTTPAPP', 'http://idaertys.mydde.fr/');
        //
        DEFINE('FLATTENIMGDIR', CUSTOMERPATH . 'images_base/');
        DEFINE('FLATTENIMGHTTP', HTTPCUSTOMERSITE . '/images_base/');
        //
        DEFINE('SOCKETIO_PORT', 3005);
        //
        DEFINE("SQL_HOST", "localhost");
        DEFINE("SQL_BDD", "crm_general_new");
        DEFINE("SQL_USER", "root");
        DEFINE("SQL_PASSWORD", "redPoi654pied");
        //
        DEFINE("MDB_HOST", "127.0.0.1");
        DEFINE("MDB_USER", "admin");
        DEFINE("MDB_PASSWORD", "gwetme2011");
        DEFINE("MDB_PREFIX", "idaenext_");
        //
        DEFINE('SMTPHOST', 'mail.croisierefr.com');
        DEFINE('SMTPUSER', 'meddy@croisierefr.com');
        DEFINE('SMTPPASS', 'new_drmeet2013');
        //
        break;
}

DEFINE('HTTPHOST', $HTTP_PREFIX . DOCUMENTDOMAIN);
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
DEFINE('PATHICC', DOCUMENTROOT . 'icc/');
DEFINE('APPROOT', DOCUMENTROOT);

require_once(ADODBDIR . "adodb.inc.php");

require_once(REPFONCTIONS_APP . "function_prod.php");
require_once(REPFONCTIONS_APP . "function.php");
require_once(REPFONCTIONS_APP . "function_site.php");

require_once(REPFONCTIONS_APP . "fonctionsDevis.php");
require_once(REPFONCTIONS_APP . "fonctionsJs.php");
include_once(REPFONCTIONS_APP . 'phpthumb/ThumbLib.inc.php');

//
if (file_exists(APPMDL . 'customer/' . CUSTOMERNAME . '/function.php')) {
    include_once(APPMDL . 'customer/' . CUSTOMERNAME . '/function.php');
}
// skel
include_once('vendor/autoload.php');

include_once(APPPATH . "web/appskel/skelStrap.php");
/*include_once(APPCLASSES . "ClassAct.php");
include_once(APPCLASSES . "ClassApp.php");*/

//
$IMG_SIZE_ARR = !empty($IMG_SIZE_ARR) ? $IMG_SIZE_ARR : ['square' => ['120', '120'], 'small' => ['210', '140'], 'large' => ['650', '430'], 'wallpaper' => ['1920', '1080']];
$buildArr = !empty($buildArr) ? $buildArr : ['tiny' => ['100', '25'], 'squary' => ['68', '68'], 'largy' => ['325', '215'], 'wallpapery' => ['100', '25']];
//

if (!function_exists(my_autoloader)) {
    function my_autoloader($class_name)
    {
        // echo APPCLASSES . '/appcommon/Class' . $class_name . '.php';
        $dirs = array(
            APPCLASSES,
            APPCLASSES . '/appcommon/',
            APPBIN . '/classes/shared/',
            OLDAPPCLASSES,
            APPBINCLASSES
        );
        foreach ($dirs as $directory) {
            //see if the file exsists
            if (file_exists($directory . 'Class' . $class_name . '.php')) {
                require($directory . 'Class' . $class_name . '.php');
                return true;
            }
        }
        $folder = APPCLASSES;
        $class_name = ltrim($class_name, '\\');
        $fileName = '';

        if ($lastNsPos = strripos($class_name, '\\')) {
            $namespace = substr($class_name, 0, $lastNsPos);
            $class_name = substr($class_name, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';
        if (file_exists($folder . $fileName)) require($folder . $fileName);
        else {
            $name_class = "\\$class_name";
            return new $name_class;
        }
    }

    spl_autoload_register('my_autoloader');
}
// SESSIONS
include_once('appclasses/ClassSession.php');

//
if (!function_exists(myddeDebug)) {
    function myddeDebug($vars)
    {
        echo "<pre>";
        var_dump($vars);
        echo "</pre>";
    }
}
/*$APP->init_scheme('sitebase_devis', 'client_type');*/

