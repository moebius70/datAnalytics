<?php

// Only display errors in the command line
error_reporting(php_sapi_name() === 'cli' ? E_ALL : 0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


define('BASE_URL', 'localhost:8000/index.php');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'RaceAnalytics');
define('DB_USER', 'root');
define('DB_PASS', 'password');

// Include core framework files
include 'framework/core/Controller.class.php';
include 'framework/core/Model.class.php';
include 'framework/core/View.class.php';
include 'Controllers/errors/NotFound.class.php';
include 'framework/Router.php';

// Autoload function to dynamically include class files from Controllers and Models
spl_autoload_register(function ($className) {
    $directories = [
        'Controllers/',
        'Models/'
    ];
    
    foreach ($directories as $directory) {
        $filePath = $directory . $className . '.class.php';
        if (file_exists($filePath)) {
            include $filePath;
            break;
        }
    }
});

?>
